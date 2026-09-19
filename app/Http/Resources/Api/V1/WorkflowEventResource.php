<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\HoSoStatus;
use App\Models\HoSoWorkflowEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin HoSoWorkflowEvent
 */
class WorkflowEventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $from = $this->from_status ? HoSoStatus::tryFrom($this->from_status) : null;
        $to = $this->to_status ? HoSoStatus::tryFrom($this->to_status) : null;

        return [
            'id' => $this->id,
            'application_id' => $this->maHSXL,
            'event_type' => $this->event_type,
            'actor' => [
                'id' => $this->actor_id,
                'name' => $this->actor_name,
                'role' => $this->actor_role,
            ],
            'from_status' => $from ? ['value' => $from->value, 'label' => $from->label()] : null,
            'to_status' => $to ? ['value' => $to->value, 'label' => $to->label()] : null,
            'note' => $this->note,
            'metadata' => $this->metadata,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
