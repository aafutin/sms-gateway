<?php

namespace App\Sms;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Имитация SMS-оператора: с заданной вероятностью «падает»,
 * а повторный запрос с тем же ключом идемпотентности не отправляет SMS второй раз.
 */
final class FakeSmsProvider implements SmsProviderInterface
{
    public function __construct(
        private readonly CacheItemPoolInterface $cache,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'float:FAKE_SMS_FAILURE_RATE')]
        private readonly float $failureRate,
    ) {
    }

    public function send(string $phone, string $text, string $idempotencyKey): string
    {
        $sent = $this->cache->getItem('fake_sms_'.$idempotencyKey);
        if ($sent->isHit()) {
            $this->logger->info('Fake provider: duplicate request, already sent', ['key' => $idempotencyKey]);

            return $sent->get();
        }

        if (mt_rand() / mt_getrandmax() < $this->failureRate) {
            throw new SmsProviderException('Fake provider: operator is unavailable');
        }

        $providerMessageId = bin2hex(random_bytes(8));
        $this->cache->save($sent->set($providerMessageId));
        $this->logger->info('Fake provider: SMS sent', ['phone' => $phone, 'providerMessageId' => $providerMessageId]);

        return $providerMessageId;
    }
}
