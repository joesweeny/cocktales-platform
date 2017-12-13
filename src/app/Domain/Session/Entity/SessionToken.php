<?php

namespace Cocktales\Domain\Session\Entity;

use Cocktales\Framework\Uuid\Uuid;

class SessionToken
{
    /**
     * @var Uuid
     */
    private $token;
    /**
     * @var Uuid
     */
    private $userId;
    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;
    /**
     * @var \DateTimeImmutable
     */
    private $expiry;

    public function __construct(Uuid $token, Uuid $userId, \DateTimeImmutable $createdAt, \DateTimeImmutable $expiry)
    {
        $this->token = $token;
        $this->userId = $userId;
        $this->createdAt = $createdAt;
        $this->expiry = $expiry;
    }

    public function getToken(): Uuid
    {
        return $this->token;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiry(): \DateTimeImmutable
    {
        return $this->expiry;
    }

    public function setExpiry(\DateTimeImmutable $expiry)
    {
        $this->expiry = $expiry;

        return $this;
    }
}
