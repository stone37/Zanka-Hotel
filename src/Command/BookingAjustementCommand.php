<?php

namespace App\Command;

use App\Manager\BookingManager;
use App\Repository\BookingRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:booking:ajustement',
    description: 'Annule tous les réservations non confirmer',
)]
class BookingAjustementCommand extends Command
{
    public function __construct(
        private BookingManager $manager,
        private BookingRepository $repository
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->progressStart();

        $bookings = $this->repository->getCancel();
        $this->manager->cancelledAjustement($bookings);

        $io->progressFinish();

        $io->success(sprintf('%s reservation(s) ont été annulée.', count($bookings)));

        return Command::SUCCESS;
    }
}
