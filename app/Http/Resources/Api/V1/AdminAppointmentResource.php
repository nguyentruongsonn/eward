<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;

class AdminAppointmentResource extends AppointmentResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        unset($data['checkin_token']);
        $data['applicant'] = $this->whenLoaded('congdan', fn (): array => [
            'id' => $this->congdan?->getKey(),
            'name' => $this->congdan?->nguoi?->hoTen,
            'email' => $this->congdan?->nguoi?->email,
        ]);

        return $data;
    }
}
