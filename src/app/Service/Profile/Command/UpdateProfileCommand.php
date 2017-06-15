<?php

namespace Cocktales\Service\Profile\Command;

use Cocktales\Framework\CommandBus\Command;

class UpdateProfileCommand implements Command
{
    /**
     * @var \stdClass
     */
    private $data;

    /**
     * UpdateProfileCommand constructor.
     * @param \stdClass $data
     */
    public function __construct(\stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->data->user_id;
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
    public function getCity(): string
    {
        return $this->data->city;
    }

    /**
     * @return string
     */
    public function getCounty(): string
    {
        return $this->data->county;
    }

    /**
     * @return string
     */
    public function getSlogan(): string
    {
        return $this->data->slogan;
    }
}
