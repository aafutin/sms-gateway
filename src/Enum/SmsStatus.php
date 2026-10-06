<?php

namespace App\Enum;

enum SmsStatus: string
{
    case New = 'new';
    case Queued = 'queued';
    case Sent = 'sent';
    case Failed = 'failed';

    public function isFinal(): bool
    {
        return match ($this) {
            self::Sent, self::Failed => true,
            self::New, self::Queued => false,
        };
    }
}
