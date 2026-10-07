<?php

namespace App\Sms;

interface SmsProviderInterface
{
    /**
     * Отправляет SMS и возвращает id сообщения у оператора.
     *
     * Повторный вызов с тем же $idempotencyKey не отправляет SMS заново,
     * а возвращает id из первого вызова.
     *
     * @throws SmsProviderException оператор недоступен или отказал
     */
    public function send(string $phone, string $text, string $idempotencyKey): string;
}
