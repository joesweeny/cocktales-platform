<?php

namespace Cocktales\Boundary\Profile\Command;

use Cocktales\Framework\CommandBus\Command;
use Cocktales\Framework\Uuid\Uuid;

class CreateProfileCommand implements Command
{
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * CreateProfileCommand constructor.
     * @param \stdClass $data
     */
    public function __construct(\stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return new Uuid($this->data->user_id);
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->data->username;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->data->first_name;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->data->last_name;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->data->location;
    }

    /**
     * @return string
     */
    public function getSlogan(): string
    {
        return $this->data->slogan;
    }
}
