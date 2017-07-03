<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Auth;

use Cocktales\Application\Http\Session\SessionAuthenticator;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\RunsMigrations;
use Cocktales\Helpers\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class LoginIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use CreatesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  SessionAuthenticator */
    private $authenticator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->authenticator = $this->container->get(SessionAuthenticator::class);
    }

    public function test_submit_logs_a_user_in_and_redirects_to_home_page()
    {
        $this->createUser();

        $request = new ServerRequest('post', '/app/auth/login', [], '{"email":"joe@mail.com","password":"password"}');

        $response = $this->handle($this->container, $request);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/app', $response->getHeader('location')[0]);
    }

    public function test_login_page_is_rendered_if_a_user_is_not_logged_in()
    {
        $this->createUser();

        $request = new ServerRequest('get', '/app/auth/login', [], '');

        $response = $this->handle($this->container, $request);

        $this->assertEquals(200, $response->getStatusCode());
    }

    // @todo More concrete tests to prove login

    private function createUser()
    {
        $this->orchestrator->createUser(
            (new User('93449e9d-4082-4305-8840-fa1673bcf915'))
                ->setEmail('joe@mail.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );
    }
}
