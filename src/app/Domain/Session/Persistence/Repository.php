<?php

namespace Cocktales\Domain\Session\Persistence;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;

interface Repository
{
    /**
     * Insert a new SessionToken
     *
     * @param SessionToken $token
     * @return SessionToken
     */
    public function insertToken(SessionToken $token): SessionToken;

    /**
     * @param SessionToken $token
     * @return SessionToken
     * @throws NotFoundException
     */
    public function updateToken(SessionToken $token): SessionToken;

    /**
     * @param Uuid $token
     * @return SessionToken
     * @throws NotFoundException
     */
    public function getToken(Uuid $token): SessionToken;
}
