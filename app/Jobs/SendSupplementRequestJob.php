<?php

namespace App\Jobs;

use App\Models\HoSoXuLy;
use App\Models\Nguoi;
use App\Services\Mail\ApplicationMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSupplementRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param list<string> $documentNames */
    public function __construct(
        public readonly string $applicationId,
        public readonly int $actorId,
        public readonly array $documentNames,
        public readonly ?string $note,
    ) {}

    public function handle(ApplicationMailService $mailService): void
    {
        $application = HoSoXuLy::query()->findOrFail($this->applicationId);
        $actor = Nguoi::query()->find($this->actorId);
        $body = "Hồ sơ {$application->getKey()} cần bổ sung các giấy tờ sau:\n- ".implode("\n- ", $this->documentNames);
        if ($this->note) {
            $body .= "\n\nGhi chú: {$this->note}";
        }

        $mailService->sendApplicationMail(
            $application,
            'Yêu cầu bổ sung hồ sơ',
            $body,
            $actor,
            'bo_sung',
        );
    }
}
