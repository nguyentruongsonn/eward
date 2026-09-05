<?php

namespace App\Services;

use App\Models\HoSoXuLy;

class PublicApplicationTrackingService
{
    public function findByCode(string $code): ?HoSoXuLy
    {
        return HoSoXuLy::query()
            ->with(['trangThai', 'tthc'])
            ->whereKey(trim($code))
            ->first();
    }

    public function applicantName(HoSoXuLy $application): string
    {
        $data = $application->dulieu;
        if (! is_array($data)) {
            $data = json_decode((string) $data, true);
        }
        if (! is_array($data)) {
            return 'N/A';
        }

        return (string) ($data['hoTen'] ?? $data['ho_ten'] ?? $data['payload']['hoTen'] ?? 'N/A');
    }

    public function findVerifiedByCode(string $code, string $verification): ?HoSoXuLy
    {
        $application = $this->findByCode($code);
        if (! $application || ! $this->matchesVerification($application, $verification)) {
            return null;
        }

        return $application;
    }

    /** @return array<string, mixed> */
    public function minimalTimeline(HoSoXuLy $application): array
    {
        $statusLabel = $application->trangThai?->tenTrangThai;
        $occurredAt = $application->ngayTra
            ?? $application->ngayKetThucXuLy
            ?? $application->ngayTiepNhan;

        return [
            'application_code' => (string) $application->maHSXL,
            'procedure_name' => $application->tthc?->tenTTHC,
            'status' => [
                'id' => (int) $application->maTrangThai,
                'label' => $statusLabel,
            ],
            'timeline' => [[
                'status_id' => (int) $application->maTrangThai,
                'status_label' => $statusLabel,
                'occurred_at' => optional($occurredAt)->toIso8601String(),
            ]],
        ];
    }

    private function matchesVerification(HoSoXuLy $application, string $verification): bool
    {
        $verification = trim($verification);
        if ($verification === '') {
            return false;
        }

        $email = strtolower(trim((string) $application->email));
        if ($email !== '' && hash_equals($email, strtolower($verification))) {
            return true;
        }

        $phone = preg_replace('/\D+/', '', (string) $application->soDienThoai);
        $candidate = preg_replace('/\D+/', '', $verification);

        return $phone !== '' && $candidate !== '' && hash_equals($phone, $candidate);
    }
}
