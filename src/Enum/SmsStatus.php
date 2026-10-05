<?php

namespace App\Enum;

enum SmsStatus: string
{
    case New = 'new';
    case Queued = 'queued';
    case Sent = 'sent';
    case Failed = 'failed';
}
