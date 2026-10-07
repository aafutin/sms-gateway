<?php

namespace App\Command;

use App\Message\SendSms;
use App\Repository\SmsMessageRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(name: 'sms:requeue-stuck', description: 'Повторно ставит в очередь SMS, зависшие в статусе new')]
final class RequeueStuckSmsCommand extends Command
{
    public function __construct(
        private readonly SmsMessageRepository $repository,
        private readonly MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('older-than', null, InputOption::VALUE_REQUIRED, 'Минут с момента приёма', 5)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, 'Сколько SMS обработать за запуск', 1000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $createdBefore = new \DateTimeImmutable(sprintf('-%d minutes', (int) $input->getOption('older-than')));
        $stuck = $this->repository->findStuck($createdBefore, (int) $input->getOption('limit'));

        // Дубли в очереди безопасны: обработчик пропустит уже отправленные SMS
        foreach ($stuck as $sms) {
            $this->bus->dispatch(new SendSms($sms->getId()->toRfc4122()));
        }

        $output->writeln(sprintf('Requeued: %d', count($stuck)));

        return Command::SUCCESS;
    }
}
