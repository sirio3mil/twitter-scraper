<?php

namespace App\Command;

use App\Model\Twitter\Counter;
use App\Service\RaffleService;
use App\Service\TwitterService;
use App\ValueObject\Twitter\User;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParseHashtagCommand extends Command
{
    /**
     * @var TwitterService
     */
    private $twitterService;
    /**
     * @var RaffleService
     */
    private $raffleService;

    /**
     * ParseHashtagCommand constructor.
     * @param TwitterService $twitterService
     * @param RaffleService $raffleService
     */
    public function __construct(TwitterService $twitterService, RaffleService $raffleService)
    {
        parent::__construct();
        $this->twitterService = $twitterService;
        $this->raffleService = $raffleService;
    }

    protected static $defaultName = 'app:parse-hashtag';

    protected function configure()
    {
        $this
            ->setDescription('Allow scrap Twitter search page by hashtag')
            ->addArgument('pattern', InputArgument::REQUIRED, 'Hashtag to search')
            ->addOption(
                'hashtag',
                null,
                InputOption::VALUE_OPTIONAL,
                'Is a hashtag pattern?',
                1
            )
            ->addOption(
                'unique',
                null,
                InputOption::VALUE_OPTIONAL,
                'Can tweet only once?',
                1
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $pattern = $input->getArgument('pattern');
        $hashtag = $input->getOption('hashtag');
        $unique = boolval($input->getOption('unique'));

        if ($pattern) {
            $io->note(sprintf('You passed an argument: %s', $pattern));
        }
        if ($hashtag) {
            $pattern = "#$pattern";
        }

        /** @var Counter[] $results */
        $results = $this->twitterService->search($pattern)->getResults();
        /** @var User $winner */
        $winner = $this->raffleService->setUnique($unique)->getWinner($results);
        /** @var Counter $counter */
        foreach ($results as $counter) {
            $io->text($counter->getUser()->getName());
        }
        $io->success($winner->getName());

        return 0;
    }
}
