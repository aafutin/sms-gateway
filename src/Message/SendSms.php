<?php

namespace App\Message;

/**
 * Команда «отправь SMS». Только id: актуальные данные воркер читает из БД,
 * а персональные данные (телефон, текст) не копируются в брокер.
 */
final readonly class SendSms
{
    public function __construct(
        public string $smsId,
    ) {
    }
}
