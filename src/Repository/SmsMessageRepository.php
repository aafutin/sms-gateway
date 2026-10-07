<?php

namespace App\Repository;

use App\Entity\SmsMessage;
use App\Enum\SmsStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SmsMessage>
 */
class SmsMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsMessage::class);
    }

    /**
     * SMS, которые давно приняты, но так и не отправлены —
     * например, RabbitMQ был недоступен в момент приёма.
     *
     * @return list<SmsMessage>
     */
    public function findStuck(\DateTimeImmutable $createdBefore, int $limit): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status = :status')
            ->andWhere('s.createdAt < :createdBefore')
            ->setParameter('status', SmsStatus::New)
            ->setParameter('createdBefore', $createdBefore)
            ->orderBy('s.createdAt')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
