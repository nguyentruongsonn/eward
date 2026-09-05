<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Write a structured audit event without persisting credentials or tokens.
     *
     * @param  array<string, mixed>  $context
     */
    public function log(string $event, array $context = []): void
    {
        Log::info('audit.'.$event, $this->redact($context));
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array<string, mixed>
     */
    private function redact(array $value): array
    {
        foreach ($value as $key => $item) {
            if (preg_match('/password|token|secret|authorization|api[_-]?key/i', (string) $key)) {
                $value[$key] = '[REDACTED]';

                continue;
            }

            if (is_array($item)) {
                $value[$key] = $this->redact($item);
            }
        }

        return $value;
    }
}
