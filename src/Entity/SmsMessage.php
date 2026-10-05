<?php

namespace App\Entity;

use App\Enum\SmsStatus;
use App\Repository\SmsMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SmsMessageRepository::class)]
#[ORM\Table(name: 'sms_message')]
class SmsMessage
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    private Uuid $id;

    #[ORM\Column(length: 16)]
    private string $phone;

    #[ORM\Column(type: Types::TEXT)]
    private string $text;

    #[ORM\Column(length: 16, enumType: SmsStatus::class)]
    private SmsStatus $status;

    #[ORM\Column]
    private int $attempts = 0;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct(string $phone, string $text)
    {
        // v7: начинается с времени — новые записи ложатся в конец B-tree индекса
        $this->id = Uuid::v7();
        $this->phone = $phone;
        $this->text = $text;
        $this->status = SmsStatus::New;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getStatus(): SmsStatus
    {
        return $this->status;
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
