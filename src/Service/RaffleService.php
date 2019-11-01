<?php

namespace App\Service;

use App\Exception\WinnerNotFoundException;
use stdClass;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class RaffleService
{
    /**
     * @var bool
     */
    protected $unique;

    /**
     * @var array
     */
    private $userData;

    /**
     * @var string
     */
    private $winnerId;

    /**
     * @param bool $unique
     * @return RaffleService
     */
    public function setUnique(bool $unique): RaffleService
    {
        $this->unique = $unique;
        return $this;
    }

    /**
     * @param string $json
     * @return RaffleService
     */
    public function calculate(string $json): RaffleService
    {
        $data = json_decode($json);
        $users = [];
        $this->userData = [];

        foreach ($data->statuses as $tweet) {
            $reTweet = $tweet->retweeted_status ?? null;
            if (!$reTweet) {
                $user = $tweet->user;
                $users[] = $user->id;
                $this->userData[$user->id] = $user;
            }
        }

        $targetUsers = ($this->unique) ? array_unique($users) : $users;
        $winnerKey = array_rand($targetUsers);
        $this->winnerId = $targetUsers[$winnerKey];

        return $this;
    }

    /**
     * @return array
     */
    public function getUserData(): array
    {
        return $this->userData;
    }

    /**
     * @return string
     */
    public function getWinnerId(): string
    {
        return $this->winnerId;
    }

    /**
     * @return stdClass
     * @throws WinnerNotFoundException
     */
    public function getWinner(): stdClass
    {
        if (!array_key_exists($this->winnerId, $this->userData)) {
            throw new WinnerNotFoundException();
        }
        return $this->userData[$this->winnerId];
    }
}
