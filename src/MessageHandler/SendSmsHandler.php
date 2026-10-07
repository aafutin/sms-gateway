<?php

namespace App\MessageHandler;

use App\Message\SendSms;
use App\Repository\SmsMessageRepository;
use App\Sms\SmsProviderException;
use App\Sms\SmsProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;

#[AsMessageHandler]
final class SendSmsHandler
{
    public function __construct(
        private readonly SmsMessageRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly SmsProviderInterface $provider,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(SendSms $message): void
    {
        $sms = $this->repository->find(Uuid::fromString($message->smsId));
        if (null === $sms) {
            $this->logger->warning('SMS not found, skipping', ['id' => $message->smsId]);

            return;
        }

        // RabbitMQ доставляет «хотя бы один раз»: повтор уже отправленной SMS просто пропускаем
        if ($sms->getStatus()->isFinal()) {
            $this->logger->info('SMS already final, skipping duplicate delivery', ['id' => $message->smsId]);

            return;
        }

        $sms->registerAttempt();

        try {
            // id SMS — ключ идемпотентности: если мы упадём после отправки, повтор не отправит её второй раз
            $providerMessageId = $this->provider->send($sms->getPhone(), $sms->getText(), $message->smsId);
        } catch (SmsProviderException $e) {
            $this->em->flush();

            // Исключение уходит в Messenger — он повторит доставку по retry_strategy
            throw $e;
        }

        $sms->markSent($providerMessageId);
        $this->em->flush();
    }
}
