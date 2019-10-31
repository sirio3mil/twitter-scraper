<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParseHashtagCommand extends Command
{
    protected static $defaultName = 'app:parse-hashtag';

    protected function configure()
    {
        $this
            ->setDescription('Allow scrap Twitter search page by hashtag')
            ->addArgument('hashtag', InputArgument::REQUIRED, 'Hashtag to search')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $hashtag = $input->getArgument('hashtag');

        if ($hashtag) {
            $io->note(sprintf('You passed an argument: %s', $hashtag));
        }



        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
