<?php

namespace App\Service;

use App\Model\Twitter\Counter;
use App\ValueObject\Twitter\Credentials;
use App\ValueObject\Twitter\User;
use Exception;
use TwitterAPIExchange;

class TwitterService
{
    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var Counter[]
     */
    private $results;

    public function __construct(Credentials $credentials)
    {
        $this->credentials = $credentials;
        $this->resetResults();
    }

    /**
     * @param string $pattern
     * @return TwitterService
     * @throws Exception
     */
    public function search(string $pattern): TwitterService
    {
        $url = 'https://api.twitter.com/1.1/search/tweets.json';
        $getField = "?q={$pattern}&count=100";
        $requestMethod = 'GET';
        $twitter = new TwitterAPIExchange($this->credentials->getSettings());
        $this->resetResults();
        do {
            $json = $twitter->setGetfield($getField)
                ->buildOauth($url, $requestMethod)
                ->performRequest();
            $this->save($json);
            $data = json_decode($json);
            foreach ($data->statuses as $tweet) {
                $reTweet = $tweet->retweeted_status ?? null;
                if (!$reTweet) {
                    $user = $tweet->user;
                    if (!array_key_exists($user->id, $this->results)) {
                        $this->results[$user->id] = new Counter(User::fromSearchResults($user));
                    }
                    else{
                        $this->results[$user->id]->increase();
                    }
                }
            }
            $getField = $data->search_metadata->next_results ?? null;
        } while ($getField);

        return $this;
    }

    /**
     * @return TwitterService
     */
    protected function resetResults(): TwitterService
    {
        $this->results = [];
        return $this;
    }

    /**
     * @param string $json
     * @return TwitterService
     */
    protected function save(string $json): TwitterService
    {
        $hash = sha1($json);
        $filename = dirname(__DIR__, 2) . "/var/tmp/{$hash}.json";
        file_put_contents($filename, $json);
        return $this;
    }

    /**
     * @return User[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
