<?php

namespace App\Services\Chat;

use App\Exceptions\ChatAssistantException;
use App\Models\TTHC;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatAssistantService
{
    public function reply(string $message): string
    {
        $configuration = config('services.groq', []);
        $apiKey = trim((string) ($configuration['api_key'] ?? ''));
        if ($apiKey === '') {
            throw new ChatAssistantException('Dịch vụ tư vấn hiện chưa sẵn sàng.');
        }

        try {
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->timeout((int) ($configuration['timeout'] ?? 20))
                ->post((string) $configuration['api_url'], [
                    'model' => $configuration['model'],
                    'messages' => [
                        ['role' => 'system', 'content' => $this->systemContext()],
                        ['role' => 'user', 'content' => $message],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 1024,
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Chat provider connection failed', ['exception' => $exception::class]);

            throw new ChatAssistantException('Dịch vụ tư vấn hiện tạm thời không khả dụng.', 0, $exception);
        }

        if ($response->failed()) {
            Log::warning('Chat provider request failed', ['status' => $response->status()]);

            throw new ChatAssistantException('Dịch vụ tư vấn hiện tạm thời không khả dụng.');
        }

        $reply = $response->json('choices.0.message.content');
        if (! is_string($reply) || trim($reply) === '') {
            Log::warning('Chat provider returned an empty response');

            throw new ChatAssistantException('Dịch vụ tư vấn hiện tạm thời không khả dụng.');
        }

        return trim($reply);
    }

    public function clearContextCache(): void
    {
        Cache::forget('chat_assistant_knowledge_context');
    }

    private function systemContext(): string
    {
        return Cache::remember('chat_assistant_knowledge_context', now()->addHours(6), function (): string {
            $context = "Bạn là Trợ lý AI của Hệ thống Một cửa điện tử Ủy ban nhân dân xã ABC. Hãy tư vấn thân thiện, ngắn gọn, chuẩn xác theo quy định.\n\n";
            $context .= "DANH SÁCH THỦ TỤC HÀNH CHÍNH TẠI UBND XÃ ABC:\n\n";

            $procedures = TTHC::query()
                ->published()
                ->with(['linhVuc', 'thanhPhanHoSos.giayTos', 'cachThucHiens', 'lephis'])
                ->orderBy('tenTTHC')
                ->get();

            foreach ($procedures as $index => $procedure) {
                $context .= ($index + 1).'. '.$procedure->tenTTHC."\n";
                if ($procedure->linhVuc) {
                    $context .= '   - Lĩnh vực: '.$procedure->linhVuc->tenLinhVuc."\n";
                }
                if ($procedure->doiTuongThucHien) {
                    $context .= '   - Đối tượng thực hiện: '.$procedure->doiTuongThucHien."\n";
                }

                if ($procedure->relationLoaded('cachThucHiens') && $procedure->cachThucHiens->isNotEmpty()) {
                    $firstMethod = $procedure->cachThucHiens->first();
                    $context .= '   - Thời hạn giải quyết: '.($firstMethod->thoiHanGiaiQuyet ?: ($firstMethod->thoiHan ? "{$firstMethod->thoiHan} ngày làm việc" : 'Theo quy định'))."\n";
                }

                if ($procedure->relationLoaded('lephis') && $procedure->lephis->isNotEmpty()) {
                    $feeTexts = $procedure->lephis->map(function ($fee): string {
                        $amount = (float) $fee->soTien;
                        $desc = $fee->moTa ? " ({$fee->moTa})" : '';

                        return $amount > 0 ? number_format($amount, 0, ',', '.').' VNĐ'.$desc : 'Miễn phí';
                    })->all();
                    $context .= '   - Lệ phí: '.implode('; ', $feeTexts)."\n";
                } else {
                    $context .= "   - Lệ phí: Miễn phí theo quy định\n";
                }

                if ($procedure->relationLoaded('thanhPhanHoSos') && $procedure->thanhPhanHoSos->isNotEmpty()) {
                    $docNames = [];
                    foreach ($procedure->thanhPhanHoSos as $tp) {
                        if ($tp->relationLoaded('giayTos')) {
                            foreach ($tp->giayTos as $gt) {
                                $docNames[] = $gt->tenGiayTo;
                            }
                        }
                    }
                    if ($docNames !== []) {
                        $context .= '   - Hồ sơ, giấy tờ cần chuẩn bị: '.Str::limit(implode('; ', array_unique($docNames)), 300)."\n";
                    }
                }

                if ($procedure->trinhTuThucHien) {
                    $context .= '   - Trình tự thực hiện: '.Str::limit($procedure->trinhTuThucHien, 150)."\n";
                }
                if ($procedure->yeuCauDieuKien && $procedure->yeuCauDieuKien !== 'Không') {
                    $context .= '   - Yêu cầu điều kiện: '.Str::limit($procedure->yeuCauDieuKien, 150)."\n";
                }
                $context .= "\n";
            }

            return $context."\nKhi trả lời, hãy dựa hoàn toàn vào dữ liệu chính thức ở trên để tư vấn chính xác về tên giấy tờ, mức lệ phí và thời hạn giải quyết cho người dân.";
        });
    }
}
