<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MailHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'application_id' => $this->maHSXL,
            'direction' => $this->direction,
            'sender_type' => $this->sender_type,
            'type' => $this->loai_mail,
            'subject' => $this->subject,
            'content' => $this->content,
            'email' => $this->email,
            'sent_at' => optional($this->sent_at)->toIso8601String(),
            'sent_by' => $this->sent_by,
            'message_id' => $this->message_id,
            'in_reply_to' => $this->in_reply_to,
            'provider_uid' => $this->provider_uid,
        ];
    }
}
