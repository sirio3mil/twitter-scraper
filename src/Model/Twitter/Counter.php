<?php

namespace App\Model\Twitter;

use App\ValueObject\Twitter\User;

class Counter
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $total;

    /**
     * TweetCounter constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->total = 1;
    }

    /**
     * @return Counter
     */
    public function increase(): Counter
    {
        $this->total++;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
