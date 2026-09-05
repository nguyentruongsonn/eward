<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Services\Admin\AdminApplicationViewService;
use App\Services\Admin\AdminAuthorizationService;
use App\Services\Mail\ApplicationMailService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMailController extends Controller
{
    public function __construct(
        private readonly AdminApplicationViewService $applications,
        private readonly AdminAuthorizationService $authorization,
        private readonly ApplicationMailService $mailService,
    ) {}

    /**
     * Send an email to the citizen from the legacy admin screen.
     */
    public function sendMailHoSo(Request $request, string $maHSXL): JsonResponse
    {
        if (! $this->authorization->isAdmin(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.'], 403);
        }

        $validated = $request->validate([
            'loai_mail' => ['required', 'in:lien_lac,bo_sung'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
        ]);

        try {
            $application = $this->applications->findForAction($maHSXL);
            if (! $application->email) {
                return response()->json(['success' => false, 'message' => 'Hồ sơ không có email.'], 422);
            }

            $this->mailService->sendApplicationMail(
                $application,
                $validated['subject'],
                $validated['content'],
                Auth::user(),
                $validated['loai_mail'],
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi mail thành công',
                'email' => $application->email,
                'last_mail_sent_at' => optional($application->last_mail_sent_at)->format('d/m/Y H:i'),
            ]);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ.'], 404);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['success' => false, 'message' => 'Lỗi khi gửi mail.'], 500);
        }
    }

    /**
     * Record a manually entered citizen reply from the legacy admin screen.
     */
    public function addMailReply(Request $request, string $maHSXL): JsonResponse
    {
        if (! $this->authorization->isAdmin(Auth::user())) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền truy cập.'], 403);
        }

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
            'email' => ['required', 'email', 'max:255'],
            'sent_at' => ['nullable', 'date'],
        ]);

        try {
            $application = $this->applications->findForAction($maHSXL);
            $this->mailService->recordManualReply($application, [
                'subject' => $validated['subject'],
                'content' => $validated['content'],
                'email' => $validated['email'],
                'sent_at' => $validated['sent_at'] ?? now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Đã thêm email reply từ công dân']);
        } catch (ModelNotFoundException) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy hồ sơ.'], 404);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['success' => false, 'message' => 'Lỗi khi thêm email reply.'], 500);
        }
    }

    /**
     * Receive provider/webhook email replies and correlate them to an application.
     */
    public function receiveMailReply(Request $request): JsonResponse
    {
        try {
            $this->verifyMailWebhookSignature($request);

            $mail = [
                'from' => $request->input('from') ?? $request->input('sender') ?? $request->input('email'),
                'subject' => (string) ($request->input('subject') ?? ''),
                'content' => (string) ($request->input('text') ?? $request->input('body') ?? $request->input('content') ?? ''),
                'message_id' => $request->input('message_id') ?? $request->input('message-id'),
                'in_reply_to' => $request->input('in_reply_to') ?? $request->input('in-reply-to'),
                'provider_uid' => $request->input('provider_uid'),
                'references' => $request->input('references'),
                'sent_at' => $request->input('timestamp') ?? $request->input('date') ?? now(),
            ];

            $history = $this->mailService->processReply($mail);

            return response()->json(['success' => true, 'message' => 'Đã nhận email reply', 'maHSXL' => $history->maHSXL]);
        } catch (ApiException $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
                'code' => $exception->errorCode,
            ], $exception->status);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json(['success' => false, 'message' => 'Không thể xử lý email reply.'], 500);
        }
    }

    private function verifyMailWebhookSignature(Request $request): void
    {
        $secret = (string) config('services.imap.webhook_secret');
        if ($secret === '') {
            throw new ApiException('Webhook email chưa được cấu hình.', 'MAIL_WEBHOOK_NOT_CONFIGURED', 503);
        }

        $signature = trim((string) $request->header('X-Mail-Webhook-Signature'));
        $signature = preg_replace('/^sha256=/i', '', $signature) ?? '';
        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        if ($signature === '' || ! hash_equals($expected, $signature)) {
            throw new ApiException('Webhook không hợp lệ.', 'MAIL_WEBHOOK_UNAUTHORIZED', 401);
        }
    }
}
