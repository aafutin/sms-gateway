<?php

namespace App\Controller;

use App\Dto\SendSmsRequest;
use App\Entity\SmsMessage;
use App\Repository\SmsMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Uid\Uuid;

final class SmsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SmsMessageRepository $repository,
    ) {
    }

    #[Route('/sms', name: 'sms_send', methods: ['POST'])]
    public function send(#[MapRequestPayload] SendSmsRequest $request): JsonResponse
    {
        $sms = new SmsMessage($request->phone, $request->text);

        $this->em->persist($sms);
        $this->em->flush();

        return $this->json(
            ['id' => $sms->getId()->toRfc4122(), 'status' => $sms->getStatus()->value],
            Response::HTTP_ACCEPTED,
        );
    }

    #[Route('/sms/{id}', name: 'sms_show', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
    public function show(Uuid $id): JsonResponse
    {
        $sms = $this->repository->find($id) ?? throw $this->createNotFoundException();

        return $this->json([
            'id' => $sms->getId()->toRfc4122(),
            'phone' => $sms->getPhone(),
            'text' => $sms->getText(),
            'status' => $sms->getStatus()->value,
            'attempts' => $sms->getAttempts(),
            'createdAt' => $sms->getCreatedAt()->format(\DATE_ATOM),
            'updatedAt' => $sms->getUpdatedAt()->format(\DATE_ATOM),
        ]);
    }
}
