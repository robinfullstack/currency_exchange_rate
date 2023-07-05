<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCronJobCommand extends Command
{
    protected static $defaultName = 'app:generate-cron-job';
    protected static $defaultDescription = 'This command generates the cron job configuration for the currency rates command. The generated cron job can be used to trigger the currency rates command daily at 1am.';

    protected function configure(): void
    {
        $this
            ->setDescription('Generates the cron job configuration for the currency rates command')
            ->addArgument('currencies', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'The currencies to fetch exchange rates for (e.g., EUR USD)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $currencies = $input->getArgument('currencies');
        $currenciesString = implode(' ', $currencies);
    
        $projectPath = $this->getApplication()->getKernel()->getProjectDir();
        $cronJob = '0 1 * * * cd '.$projectPath.' && php bin/console app:currency:rates '.$currenciesString.' --base-currency=EUR >> '.$projectPath.'/var/log/cron.log 2>&1';
    
        $output->writeln($cronJob);
    
        return Command::SUCCESS;
    }    
}
