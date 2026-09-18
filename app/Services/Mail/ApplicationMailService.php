<?php

namespace App\Services\Mail;

use App\Exceptions\ApiException;
use App\Mail\HoSoMail;
use App\Models\HoSoXuLy;
use App\Models\HoSoXuLyMailHistory;
use App\Models\Nguoi;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApplicationMailService
{
    public function __construct(private readonly MailContentNormalizer $normalizer) {}

    public function paginateHistory(HoSoXuLy $application, ?string $direction = null, int $perPage = 20): LengthAwarePaginator
    {
        $query = $application->mailHistory()
            ->orderByDesc('sent_at')
            ->orderByDesc('id');
        if ($direction !== null && $direction !== '') {
            $query->where('direction', $direction);
        }

        return $query->paginate(max(1, min($perPage, 100)))->withQueryString();
    }

    public function sendApplicationMail(
        HoSoXuLy $application,
        string $subject,
        string $body,
        ?Nguoi $sender = null,
        string $type = 'lien_lac',
    ): HoSoXuLyMailHistory {
        $messageId = '<eward-'.Str::uuid().'@'.parse_url(config('app.url'), PHP_URL_HOST).'>';
        $subjectWithReference = trim($subject).' [HSXL:'.$application->getKey().']';
        Mail::to($application->email)->send(new HoSoMail($application, $subjectWithReference, $body, $type, $messageId));

        $history = HoSoXuLyMailHistory::create([
            'maHSXL' => $application->getKey(),
            'direction' => 'outgoing',
            'sender_type' => 'admin',
            'loai_mail' => $type,
            'subject' => $subjectWithReference,
            'content' => $body,
            'email' => $application->email,
            'sent_at' => now(),
            'sent_by' => $sender?->getKey(),
            'message_id' => $messageId,
        ]);

        $application->forceFill(['last_mail_sent_at' => now()])->save();

        return $history;
    }

    public function recordManualReply(HoSoXuLy $application, array $mail): HoSoXuLyMailHistory
    {
        return HoSoXuLyMailHistory::create([
            'maHSXL' => $application->getKey(),
            'direction' => 'incoming',
            'sender_type' => 'citizen',
            'loai_mail' => 'lien_lac',
            'subject' => (string) ($mail['subject'] ?? ''),
            'content' => (string) ($mail['content'] ?? ''),
            'email' => strtolower(trim((string) ($mail['email'] ?? $application->email))),
            'sent_at' => $mail['sent_at'] ?? now(),
        ]);
    }

    /** @param array{from:string,subject:string,content:string,message_id?:string,in_reply_to?:string,provider_uid?:string,sent_at?:string} $mail */
    public function processReply(array $mail): HoSoXuLyMailHistory
    {
        $mail['subject'] = $this->normalizer->subject($mail['subject'] ?? null);
        $mail['content'] = $this->normalizer->body($mail['content'] ?? null);

        $messageId = $mail['message_id'] ?? null;
        if ($messageId && ($existing = HoSoXuLyMailHistory::query()->where('message_id', $messageId)->first())) {
            return $existing;
        }
        if (! empty($mail['provider_uid']) && ($existing = HoSoXuLyMailHistory::query()->where('provider_uid', $mail['provider_uid'])->first())) {
            return $existing;
        }

        $outgoing = null;
        $references = array_filter(array_unique(array_merge(
            [$mail['in_reply_to'] ?? null],
            preg_split('/\s+/', trim((string) ($mail['references'] ?? ''))) ?: [],
        )));
        if ($references !== []) {
            $outgoing = HoSoXuLyMailHistory::query()->whereIn('message_id', $references)->first();
        }

        if (! $outgoing && preg_match('/\[HSXL:([^\]]+)\]/i', (string) ($mail['subject'] ?? ''), $match)) {
            $outgoing = HoSoXuLyMailHistory::query()->where('maHSXL', $match[1])->latest('id')->first();
        }

        if (! $outgoing) {
            throw new ApiException('Không xác định được hồ sơ của email reply.', 'MAIL_APPLICATION_NOT_FOUND', 422);
        }

        return HoSoXuLyMailHistory::create([
            'maHSXL' => $outgoing->maHSXL,
            'direction' => 'incoming',
            'sender_type' => 'citizen',
            'loai_mail' => 'lien_lac',
            'subject' => (string) ($mail['subject'] ?? ''),
            'content' => (string) ($mail['content'] ?? ''),
            'email' => strtolower(trim((string) ($mail['from'] ?? ''))),
            'sent_at' => $mail['sent_at'] ?? now(),
            'message_id' => $messageId,
            'in_reply_to' => $mail['in_reply_to'] ?? null,
            'provider_uid' => $mail['provider_uid'] ?? null,
        ]);
    }
}
