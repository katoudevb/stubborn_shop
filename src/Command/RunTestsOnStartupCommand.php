<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'app:run-startup-tests')]
class RunTestsOnStartupCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Lancement des tests au démarrage...</info>');

        $process = new Process(['php', 'bin/phpunit']);
        $process->setTty(false);
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return $process->isSuccessful() ? Command::SUCCESS : Command::FAILURE;
    }
}
