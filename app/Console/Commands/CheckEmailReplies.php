<?php

namespace App\Console\Commands;

use App\Exceptions\ApiException;
use App\Models\HoSoXuLy;
use App\Models\HoSoXuLyMailHistory;
use App\Services\Mail\ApplicationMailService;
use App\Services\Mail\MailContentNormalizer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckEmailReplies extends Command
{
    protected $signature = 'email:check-replies {--daemon : Chạy liên tục (daemon mode)} {--interval=30 : Khoảng thời gian giữa các lần kiểm tra (giây)}';

    protected $description = 'Kiểm tra email inbox để nhận email reply từ công dân (qua IMAP)';

    public function __construct(
        private readonly ApplicationMailService $mailService,
        private readonly MailContentNormalizer $normalizer,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $daemon = (bool) $this->option('daemon');
        $interval = max(1, (int) $this->option('interval'));

        if (! $daemon) {
            return $this->checkEmails();
        }

        $this->info("🚀 Bắt đầu chạy daemon mode - kiểm tra email mỗi {$interval} giây...");
        $this->info('Nhấn Ctrl+C để dừng.');
        $this->newLine();

        while (true) {
            try {
                $this->checkEmails();
                $this->info("⏳ Đợi {$interval} giây trước lần kiểm tra tiếp theo...");
                sleep($interval);
                $this->newLine();
            } catch (\Throwable $exception) {
                $this->error('Lỗi trong daemon: '.$exception->getMessage());
                Log::error('Lỗi trong email daemon: '.$exception->getMessage());
                sleep(5);
            }
        }
    }

    private function checkEmails(): int
    {
        $this->info('Bắt đầu kiểm tra email reply...');

        if (! function_exists('imap_open')) {
            $this->error('PHP IMAP extension chưa được bật!');
            $this->warn('Vui lòng bật extension imap trong php.ini hoặc dùng webhook endpoint: POST /webhook/email-reply');

            return 1;
        }

        $imap = config('services.imap', []);
        $imapHost = (string) ($imap['host'] ?? 'imap.gmail.com');
        $imapPort = (int) ($imap['port'] ?? 993);
        $imapUsername = $imap['username'] ?? null;
        $imapPassword = trim((string) ($imap['password'] ?? ''), "\"'");
        $imapFolder = (string) ($imap['folder'] ?? 'INBOX');

        if (! $imapUsername || $imapPassword === '') {
            $this->error('Chưa cấu hình MAIL_USERNAME hoặc MAIL_PASSWORD trong .env');

            return 1;
        }

        $connection = null;

        try {
            $mailbox = "{{$imapHost}:{$imapPort}/imap/ssl}{$imapFolder}";
            $this->info("Đang kết nối đến: {$imapHost}...");
            $connection = $this->openConnection($mailbox, (string) $imapUsername, $imapPassword);

            if (! $connection) {
                $errors = function_exists('imap_errors') ? (imap_errors() ?: []) : [];
                $error = $errors !== [] ? implode('; ', $errors) : imap_last_error();
                $this->error('Không thể kết nối IMAP: '.($error ?: 'Unknown error'));

                return 1;
            }

            $this->info('✓ Đã kết nối IMAP thành công!');
            $citizenEmails = $this->citizenEmails();

            if ($citizenEmails === []) {
                $this->warn('Không tìm thấy email công dân nào trong hệ thống');

                return 0;
            }

            $this->info('Tìm thấy '.count($citizenEmails).' email công dân trong hệ thống');
            $since = date('d-M-Y', strtotime('-30 days'));
            $this->info("Đang tìm email từ công dân từ {$since}...");
            $emails = @imap_search($connection, 'SINCE "'.$since.'"');

            if (! is_array($emails) || $emails === []) {
                $this->info('Không tìm thấy email nào từ '.$since);

                return 0;
            }

            $this->info('Đang kiểm tra '.count($emails).' email trong inbox...');
            $processed = 0;
            $skippedNoHoSo = 0;
            $skippedNotReply = 0;
            $skippedExists = 0;

            foreach ($emails as $emailNumber) {
                try {
                    $header = @imap_headerinfo($connection, $emailNumber);
                    if (! $header || ! isset($header->from[0])) {
                        continue;
                    }

                    $fromEmail = strtolower(trim($header->from[0]->mailbox.'@'.$header->from[0]->host));
                    if (! in_array($fromEmail, $citizenEmails, true)) {
                        $skippedNoHoSo++;

                        continue;
                    }

                    $content = $this->readContent($connection, $emailNumber);
                    if ($content === '') {
                        continue;
                    }

                    $messageId = $this->headerString($header, 'message_id');
                    $inReplyTo = $this->headerString($header, 'in_reply_to');
                    $references = $this->headerString($header, 'references');
                    $providerUid = function_exists('imap_uid') ? (string) (@imap_uid($connection, $emailNumber) ?: '') : '';
                    $history = $this->mailService->processReply([
                        'from' => $fromEmail,
                        'subject' => $this->headerString($header, 'subject'),
                        'content' => $content,
                        'message_id' => $messageId !== '' ? $messageId : null,
                        'in_reply_to' => $inReplyTo !== '' ? $inReplyTo : null,
                        'references' => $references !== '' ? $references : null,
                        'provider_uid' => $providerUid !== '' ? $providerUid : null,
                        'sent_at' => Carbon::parse($header->date ?? now()),
                    ]);

                    if (! $history->wasRecentlyCreated) {
                        $skippedExists++;

                        continue;
                    }

                    $processed++;
                    $this->info('✓ Đã lưu email reply: '.$history->subject);
                } catch (ApiException $exception) {
                    if ($exception->errorCode === 'MAIL_APPLICATION_NOT_FOUND') {
                        $skippedNotReply++;

                        continue;
                    }

                    $this->warn("  - Lỗi khi xử lý email #{$emailNumber}: ".$exception->getMessage());
                } catch (\Throwable $exception) {
                    $this->warn("  - Lỗi khi xử lý email #{$emailNumber}: ".$exception->getMessage());
                }
            }

            $this->info("\n✓ Hoàn thành! Đã xử lý {$processed} email reply mới.");
            $this->line("Bỏ qua: {$skippedNoHoSo} không thuộc hồ sơ, {$skippedNotReply} không xác định được reply, {$skippedExists} đã tồn tại.");

            return 0;
        } catch (\Throwable $exception) {
            $this->error('Lỗi: '.$exception->getMessage());
            Log::error('Lỗi khi check email reply: '.$exception->getMessage());

            throw $exception;
        } finally {
            if ($connection) {
                @imap_close($connection);
            }
        }
    }

    /** @return list<string> */
    private function citizenEmails(): array
    {
        $fromApplications = HoSoXuLy::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->distinct()
            ->pluck('email')
            ->all();
        $fromHistory = HoSoXuLyMailHistory::query()
            ->where('direction', 'outgoing')
            ->distinct()
            ->pluck('email')
            ->all();

        return array_values(array_unique(array_filter(array_map(
            static fn ($email): string => strtolower(trim((string) $email)),
            array_merge($fromApplications, $fromHistory),
        ))));
    }

    private function readContent($connection, int $emailNumber): string
    {
        $structure = @imap_fetchstructure($connection, $emailNumber);
        $content = $structure ? $this->getTextFromStructure($connection, $emailNumber, $structure) : '';

        if ($content !== '') {
            return $this->normalizer->body($content);
        }

        $body = @imap_body($connection, $emailNumber);

        return $body ? $this->normalizer->body($body) : '';
    }

    private function openConnection(string $mailbox, string $username, string $password): mixed
    {
        set_error_handler(static function (int $severity): bool {
            return $severity === E_WARNING;
        });

        try {
            return imap_open($mailbox, $username, $password);
        } finally {
            restore_error_handler();
        }
    }

    private function headerString(object $header, string $property): string
    {
        return trim((string) ($header->{$property} ?? ''));
    }

    /**
     * Lấy text content từ email structure (hỗ trợ multipart).
     */
    private function getTextFromStructure($connection, int $emailNumber, object $structure, string $partNumber = ''): string
    {
        if ((int) ($structure->type ?? -1) === 0) {
            $body = @imap_fetchbody($connection, $emailNumber, $partNumber ?: '1');
            if (! $body) {
                return '';
            }

            $body = match ((int) ($structure->encoding ?? 0)) {
                3 => base64_decode($body) ?: '',
                4 => quoted_printable_decode($body),
                default => $body,
            };

            if (strtolower((string) ($structure->subtype ?? 'plain')) === 'html') {
                $body = strip_tags(html_entity_decode($body, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                $body = preg_replace('/\s+/', ' ', $body) ?? $body;
            }

            if (function_exists('mb_convert_encoding')) {
                $charset = $this->charsetFromStructure($structure);
                if ($charset !== '' && strtoupper($charset) !== 'UTF-8') {
                    $body = mb_convert_encoding($body, 'UTF-8', $charset);
                }
            }

            return trim($body);
        }

        if ((int) ($structure->type ?? -1) !== 1 || ! isset($structure->parts) || ! is_array($structure->parts)) {
            return '';
        }

        $textContent = '';
        foreach ($structure->parts as $index => $part) {
            $partNum = $partNumber !== '' ? $partNumber.'.'.($index + 1) : (string) ($index + 1);
            $partText = $this->getTextFromStructure($connection, $emailNumber, $part, $partNum);
            if ($partText !== '' && ($textContent === '' || strtolower((string) ($part->subtype ?? '')) === 'plain')) {
                $textContent = $partText;
            }
        }

        return $textContent;
    }

    private function charsetFromStructure(object $structure): string
    {
        foreach ($structure->parameters ?? [] as $parameter) {
            if (strtolower((string) ($parameter->attribute ?? '')) === 'charset') {
                return (string) ($parameter->value ?? '');
            }
        }

        return '';
    }
}
