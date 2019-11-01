<?php

namespace App\ValueObject\Twitter;

use stdClass;

class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $screenName;

    /**
     * User constructor.
     * @param int $id
     * @param string $name
     * @param string $screenName
     */
    public function __construct(int $id, string $name, string $screenName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->screenName = $screenName;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getScreenName(): string
    {
        return $this->screenName;
    }

    /**
     * @param stdClass $user
     * @return User
     */
    public static function fromSearchResults(stdClass $user): User
    {
        return new User($user->id, $user->name, $user->screen_name);
    }
}
