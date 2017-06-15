<?php

namespace Cocktales\Application\Http\Session;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UndefinedException;
use Cocktales\Framework\Uuid\Uuid;
use Psr\Http\Message\ServerRequestInterface;

class SessionAuthenticator
{
    /**
     * @var SessionManager
     */
    private $manager;
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;

    /**
     * SessionAuthenticator constructor.
     * @param SessionManager $manager
     * @param UserOrchestrator $orchestrator
     */
    public function __construct(SessionManager $manager, UserOrchestrator $orchestrator)
    {
        $this->manager = $manager;
        $this->orchestrator = $orchestrator;
    }

    /**
     * @param string $email
     * @param string $password
     * @param ServerRequestInterface $request
     * @return bool
     * @throws UndefinedException
     */
    public function login(string $email, string $password, ServerRequestInterface $request): bool
    {
        try {
            $user = $this->orchestrator->getUserByEmail($email);

            if ($user->getPasswordHash()->verify($password)) {
                $this->manager->set($request, 'user:logged_in', $user->getId()->__toString());
                return true;
            }

            return false;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function isLoggedIn(ServerRequestInterface $request): bool
    {
        try {
            return $this->manager->get($request, 'user:logged_in') && $this->getUser($request) instanceof User;
        } catch (NotFoundException $e) {
            return false;
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @return User
     * @throws \Cocktales\Framework\Exception\NotFoundException
     */
    public function getUser(ServerRequestInterface $request): User
    {
        return $this->orchestrator->getUserById(
            new Uuid($this->manager->get($request, 'user:logged_in'))
        );
    }

    /**
     * Clears the session and any attributes assigned to it when logging in
     *
     * @param ServerRequestInterface $request
     * @return void
     */
    public function logout(ServerRequestInterface $request)
    {
        $this->manager->destroy($request);
    }
}
