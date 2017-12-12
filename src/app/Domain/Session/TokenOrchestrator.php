<?php

namespace Cocktales\Domain\Session;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\Factory\SessionTokenFactory;
use Cocktales\Domain\Session\Persistence\Repository;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Uuid\Uuid;

class TokenOrchestrator
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var SessionTokenFactory
     */
    private $factory;

    public function __construct(Repository $repository, Clock $clock, SessionTokenFactory $factory)
    {
        $this->repository = $repository;
        $this->clock = $clock;
        $this->factory = $factory;
    }

    public function createToken(Uuid $userId): SessionToken
    {
        return $this->repository->insertToken($this->factory->createLongToken($userId));
    }

    /**
     * @param Uuid $token
     * @return SessionToken
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getToken(Uuid $token): SessionToken
    {
        return $this->repository->getToken($token);
    }

    /**
     * @param SessionToken $token
     * @return SessionToken
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function updateToken(SessionToken $token): SessionToken
    {
        return $this->repository->updateToken($token);
    }
}
