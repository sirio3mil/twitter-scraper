<?php

namespace App\Service;

use Exception;
use TwitterAPIExchange;

class TwitterService
{
    /**
     * @var string
     */
    protected $oauthAccessToken;

    /**
     * @var string
     */
    protected $oauthAccessTokenSecret;

    /**
     * @var string
     */
    protected $consumerKey;

    /**
     * @var string
     */
    protected $consumerSecret;

    public function __construct(string $consumerKey, string $consumerSecret, string $oauthAccessToken, string $oauthAccessTokenSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->oauthAccessToken = $oauthAccessToken;
        $this->oauthAccessTokenSecret = $oauthAccessTokenSecret;
    }

    /**
     * @return array
     */
    protected function getSettings(): array
    {
        return [
            'oauth_access_token' => $this->oauthAccessToken,
            'oauth_access_token_secret' => $this->oauthAccessTokenSecret,
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret
        ];
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
        $twitter = new TwitterAPIExchange($this->getSettings());
        return $twitter->setGetfield($getField)
            ->buildOauth($url, $requestMethod)
            ->performRequest();
    }
}
