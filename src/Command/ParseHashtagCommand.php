<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TwitterAPIExchange;

class ParseHashtagCommand extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
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
        $unique = $input->getOption('unique');

        if ($pattern) {
            $io->note(sprintf('You passed an argument: %s', $pattern));
        }
        if ($hashtag) {
            $pattern = "#$pattern";
        }

        $settings = [
            'oauth_access_token' => $this->container->getParameter('twitter.access.token'),
            'oauth_access_token_secret' => $this->container->getParameter('twitter.access.token.secret'),
            'consumer_key' => $this->container->getParameter('twitter.api.key'),
            'consumer_secret' => $this->container->getParameter('twitter.api.secret.key')
        ];

        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getField = "?q={$pattern}&count=100";
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $json = $twitter->setGetfield($getField)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $hash = sha1($json);
        $filename = "./var/tmp/{$hash}.json";
        file_put_contents($filename, $json);

        $data = json_decode($json);
        $users = [];
        $userData = [];

        foreach ($data->statuses as $tweet) {
            $reTweet = $tweet->retweeted_status ?? null;
            if (!$reTweet) {
                $user = $tweet->user;
                $users[] = $user->id;
                $userData[$user->id] = $user;
                $io->text($user->name);
            }
        }

        $targetUsers = ($unique) ? array_unique($users) : $users;
        $winnerKey = array_rand($targetUsers);
        $userId = $targetUsers[$winnerKey];
        $io->success($userData[$userId]->name);

        return 0;
    }
}
