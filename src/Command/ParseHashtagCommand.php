<?php

namespace App\Command;

use Curl\Curl;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Flex\CurlDownloader;

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

        $url = "https://twitter.com/search";
        $filename = "./var/tmp/matches.html";

        $curl = new Curl();
        $curl->get($url, [
            'q' => "#{$hashtag}",
            'src' => 'typed_query'
        ]);

        $bytes = file_put_contents($filename, $curl->getResponse());

        $io->success(sprintf('Wrote %d bytes', $bytes));

        return 0;
    }
}
