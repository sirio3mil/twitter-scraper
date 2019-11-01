<?php

namespace App\Service;

use App\Model\Twitter\Counter;
use App\ValueObject\Twitter\User;
use UnexpectedValueException;

class RaffleService
{
    /**
     * @var bool
     */
    protected $unique;

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
     * @param Counter[] $results
     * @return User
     */
    public function getWinner(array $results): User
    {
        if (!$this->unique) {
            throw new UnexpectedValueException();
        }

        $key = array_rand($results);

        return $results[$key]->getUser();
    }
}
