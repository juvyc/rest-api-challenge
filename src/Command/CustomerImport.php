<?php
namespace App\Command;

use Psr\Log\LoggerInterface;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use App\Service\SourceCrawler;

#[AsCommand(name: 'app:customer-import')]

class CustomerImportCommand extends Command
{
    public function __construct(
        private LoggerInterface $logger,
        private SourceCrawler $crawler,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $num_pages = $input->getOption('numpages');
        
        //Letting user know what this command is going to do
        $output->writeln([
            'Customer Importer (It will import 20 records per batch to prevent server crashing)',
            'To reach the minimum 100 records, please put the numpages value with "5"',
            '==================',
        ]);

        $nump = (int) $num_pages;
        //Show how many pages needs to finish up
        $output->writeln('Running : ' . $nump . ' page(s) which has 20 records per page.');

        //Loop based on the given numpages value
        for($_i = 1; $_i <= $nump; $_i++){
            //Do the importing
            $output->writeln('Importing Page: ' . $_i);
            $output->writeln($this->crawler->syncer($_i));
        }

        $output->writeln('============');
        $output->writeln('All done! Thank you!');
       
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        //Adding option to allow user to import many records

        $this
            ->setDescription('Run customer importer 20 records per page')
            ->setHelp('This command will import customers from the source going to local database')
            ->addOption(
                'numpages',
                null,
                InputOption::VALUE_REQUIRED,
                'To reach the minimum 100 records, please put the numpages value with "5"',
                1
            )
        ;
    }
}