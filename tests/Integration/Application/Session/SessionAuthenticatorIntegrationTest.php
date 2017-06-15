<?php

namespace Cocktales\Application\Session;

use Cocktales\Application\Http\Session\SessionAuthenticator;
use Cocktales\Application\Http\Session\SessionManager;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use Cocktales\Helpers\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class SessionAuthenticatorIntegrationTest extends TestCase
{
    use CreatesContainer;
    use RunsMigrations;
    use UsesHttpServer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  SessionAuthenticator */
    private $authenticator;
    /** @var  SessionManager */
    private $manager;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->manager = $this->prophesize(SessionManager::class);
        $this->orchestrator->createUser((new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setEmail('joe@example.com')
            ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );
        $this->authenticator = new SessionAuthenticator($this->manager->reveal(), $this->orchestrator);
    }

    public function test_login_returns_true_if_user_credentials_are_correct_and_sets_session_key_to_user_id()
    {
        $request = new ServerRequest('get', '/');

        $this->manager->set($request, 'user:logged_in', 'dc5b6421-d452-4862-b741-d43383c3fe1d')->shouldBeCalled();

        $this->assertTrue($this->authenticator->login('joe@example.com', 'password', $request));
    }

    public function test_login_returns_false_if_user_credentials_are_incorrect()
    {
        $request = new ServerRequest('get', '/');

        $this->assertFalse($this->authenticator->login('joe@example.com', 'wrong-password', $request));
    }

    public function test_isLoggedIn_returns_true_if_a_user_has_already_logged_in()
    {
        $request = new ServerRequest('get', '/');

        $this->manager->set($request, 'user:logged_in', 'dc5b6421-d452-4862-b741-d43383c3fe1d')->shouldBeCalled()->willReturn(true);

        $this->assertTrue($this->authenticator->login('joe@example.com', 'password', $request));

        $request = (new ServerRequest('get', '/'));

        $this->manager->get($request, 'user:logged_in')->shouldBeCalled()->willReturn('dc5b6421-d452-4862-b741-d43383c3fe1d');

        $this->assertTrue($this->authenticator->isLoggedIn($request));
    }

    public function test_isLoggedIn_returns_false_if_a_user_has_not_logged_in()
    {
        $request = new ServerRequest('get', '/');

        $this->manager->get($request, 'user:logged_in')->shouldBeCalled()->willReturn('');

        $this->assertFalse($this->authenticator->isLoggedIn($request));
    }

    public function test_logout_destroys_the_session_and_logs_the_user_out()
    {
        $request = new ServerRequest('get', '/');

        $this->manager->set($request, 'user:logged_in', 'dc5b6421-d452-4862-b741-d43383c3fe1d')->shouldBeCalled()->willReturn(true);

        $this->assertTrue($this->authenticator->login('joe@example.com', 'password', $request));

        $request = (new ServerRequest('get', '/'));

        $this->manager->get($request, 'user:logged_in')->shouldBeCalled()->willReturn('dc5b6421-d452-4862-b741-d43383c3fe1d');

        $this->assertTrue($this->authenticator->isLoggedIn($request));

        $this->manager->destroy($request)->shouldBeCalled();

        $this->authenticator->logout($request);

        $this->manager->get($request, 'user:logged_in')->shouldBeCalled()->willReturn(null);
        
        $this->assertFalse($this->authenticator->isLoggedIn($request));

    }
}
