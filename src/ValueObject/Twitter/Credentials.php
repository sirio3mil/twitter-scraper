<?php

namespace App\ValueObject\Twitter;

class Credentials
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
     * @return string
     */
    public function getOauthAccessToken(): string
    {
        return $this->oauthAccessToken;
    }

    /**
     * @return string
     */
    public function getOauthAccessTokenSecret(): string
    {
        return $this->oauthAccessTokenSecret;
    }

    /**
     * @return string
     */
    public function getConsumerKey(): string
    {
        return $this->consumerKey;
    }

    /**
     * @return string
     */
    public function getConsumerSecret(): string
    {
        return $this->consumerSecret;
    }

    /**
     * @return array
     */
    public function getSettings(): array
    {
        return [
            'oauth_access_token' => $this->oauthAccessToken,
            'oauth_access_token_secret' => $this->oauthAccessTokenSecret,
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret
        ];
    }
}
