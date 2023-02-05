<?php

namespace App\Command;

use App\Manager\OrphanageManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:uploader:clear-orphans',
    description: 'Effacez les téléchargements orphelins en fonction de l\'âge maximum que vous avez défini dans votre configuration.',
)]
class UploaderClearOrphansCommand extends Command
{
    public function __construct(private OrphanageManager $manager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->progressStart();

        $this->manager->clear();

        $io->progressFinish();

        $io->success('Les téléchargements d\'images orphelines ont été effacés');

        return Command::SUCCESS;
    }
}
