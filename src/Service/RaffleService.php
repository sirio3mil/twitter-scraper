<?php

namespace App\Service;

use App\Model\Twitter\Counter;
use App\ValueObject\Twitter\User;

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
            $keys = [];
            foreach ($results as $counter) {
                $id = $counter->getUser()->getId();
                $total = $counter->getTotal();
                for ($i = 0; $i < $total; ++$i) {
                    $keys[] = $id;
                }
            }
            $index = array_rand($keys);
            $key = $keys[$index];
        } else {
            $key = array_rand($results);
        }
        return $results[$key]->getUser();
    }
}
