<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
            ->addArgument('hashtag', InputArgument::REQUIRED, 'Hashtag to search')
        ;
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
        $hashtag = $input->getArgument('hashtag');

        if ($hashtag) {
            $io->note(sprintf('You passed an argument: %s', $hashtag));
        }

        $settings = [
            'oauth_access_token' => $this->container->getParameter('twitter.access.token'),
            'oauth_access_token_secret' => $this->container->getParameter('twitter.access.token.secret'),
            'consumer_key' => $this->container->getParameter('twitter.api.key'),
            'consumer_secret' => $this->container->getParameter('twitter.api.secret.key')
        ];

        $url = 'https://api.twitter.com/1.1/search/tweets.json';
            $getField = "?q=#{$hashtag}&count=100";
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($settings);
        $json =  $twitter->setGetfield($getField)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $data = json_decode($json);

        $users = [];

        foreach ($data->statuses as $tweet){
            $reTweet = $tweet->retweeted_status ?? null;
            if (!$reTweet){
                $user = $tweet->user->screen_name;
                $users[] = $user;
                $io->text($user);
            }
        }

        $winnerKey = array_rand($users);

        $io->success($users[$winnerKey]);

        return 0;
    }
}
