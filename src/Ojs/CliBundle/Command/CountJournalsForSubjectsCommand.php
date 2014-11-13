<?php

namespace Ojs\CliBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountJournalsForSubjectsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
                ->setName('ojs:count:journals:subjects')
                ->setDescription('Count journals for all subjects and update all Subject entities\' total_journal_count field.');
    }

    /**
     * @todo not implemented yet
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Counting all journals grouped by Subjects</info>\n");
        $kernel = $this->getContainer()->get('kernel');
        $application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        $application->setAutoExit(false);
    }

}
