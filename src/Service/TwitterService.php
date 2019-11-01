<?php

namespace App\Service;

use App\ValueObject\Twitter\Credentials;
use Exception;
use TwitterAPIExchange;

class TwitterService
{
    /**
     * @var Credentials
     */
    private $credentials;

    public function __construct(Credentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @param string $pattern
     * @return string
     * @throws Exception
     */
    public function search(string $pattern): string
    {
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getField = "?q={$pattern}&count=100";
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($this->credentials->getSettings());
        return $twitter->setGetfield($getField)
            ->buildOauth($url, $requestMethod)
            ->performRequest();
    }
}
