<?php

namespace App\Services\Mail;

class MailContentNormalizer
{
    public function subject(?string $subject): string
    {
        $subject = trim((string) $subject);
        if ($subject === '') {
            return '';
        }

        if (function_exists('imap_mime_header_decode')) {
            $parts = imap_mime_header_decode($subject);
            $decoded = '';
            foreach ($parts as $part) {
                $text = (string) ($part->text ?? '');
                $charset = strtoupper((string) ($part->charset ?? 'DEFAULT'));
                if ($charset !== 'DEFAULT' && $charset !== 'UTF-8' && $charset !== 'US-ASCII') {
                    $text = $this->toUtf8($text, $charset);
                }
                $decoded .= $text;
            }

            return $decoded;
        }

        return (string) preg_replace_callback(
            '/=\?([^?]+)\?([QB])\?([^?]+)\?=/i',
            function (array $matches): string {
                $charset = $matches[1];
                $encoding = strtoupper($matches[2]);
                $text = $matches[3];

                if ($encoding === 'Q') {
                    $text = quoted_printable_decode(str_replace('_', ' ', $text));
                } else {
                    $text = base64_decode($text, true) ?: '';
                }

                return strtoupper($charset) === 'UTF-8' || strtoupper($charset) === 'US-ASCII'
                    ? $text
                    : $this->toUtf8($text, $charset);
            },
            $subject,
        );
    }

    public function body(?string $body): string
    {
        $body = (string) $body;
        if ($body === '') {
            return '';
        }

        $compact = preg_replace('/\s+/', '', $body);
        if ($compact !== null && $compact !== '' && strlen($compact) % 4 === 0) {
            $decoded = base64_decode($compact, true);
            if ($decoded !== false && base64_encode($decoded) === $compact) {
                $body = $decoded;
            }
        }

        $body = quoted_printable_decode($body);
        $body = html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $body));
    }

    private function toUtf8(string $value, string $charset): string
    {
        return function_exists('mb_convert_encoding')
            ? mb_convert_encoding($value, 'UTF-8', $charset)
            : $value;
    }
}
