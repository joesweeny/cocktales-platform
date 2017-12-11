<?php

namespace Cocktales\Domain\Session\Persistence;

use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\Hydration\Extractor;
use Cocktales\Domain\Session\Hydration\Hydrator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateDbSessionTokenRepository implements Repository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritdoc
     */
    public function insertToken(SessionToken $token): SessionToken
    {
        $this->table()->insert((array) Extractor::toRawData($token));

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function updateToken(SessionToken $token): SessionToken
    {
        $builder = $this->table()->where('token', $token->getToken()->toBinary());

        if (!$builder->exists()) {
            throw new NotFoundException("Token with value {$token->getToken()} does not exist");
        }

        $builder->update((array) Extractor::toRawData($token));

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function getToken(Uuid $token): SessionToken
    {
        if (!$row = $this->table()->where('token', $token->getToken()->toBinary())->first()) {
            throw new NotFoundException("Token with value {$token} does not exist");
        }

        return Hydrator::fromRawData($row);
    }

    private function table(): Builder
    {
        return $this->connection->table('session_token');
    }
}