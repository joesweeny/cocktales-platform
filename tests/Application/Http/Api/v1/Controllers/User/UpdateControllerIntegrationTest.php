<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\User;

use Cocktales\Bootstrap\Config;
use Cocktales\Domain\Session\Entity\SessionToken;
use Cocktales\Domain\Session\TokenOrchestrator;
use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\UserOrchestrator;
use Cocktales\Framework\Password\PasswordHash;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Cocktales\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class UpdateControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  User */
    private $user;
    /** @var  SessionToken */
    private $token;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(UserOrchestrator::class);
        $this->container->get(Config::class)->set('log.logger', 'null');
        $this->user = $this->orchestrator->createUser(
            (new User('93449e9d-4082-4305-8840-fa1673bcf915'))
                ->setEmail('joe@mail.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );
        $this->token = $this->container->get(TokenOrchestrator::class)->createToken($this->user->getId());
    }

    public function test_success_response_is_received_joe()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/user/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"93449e9d-4082-4305-8840-fa1673bcf915","email":"joe@newEmail.com","password":"password", "newPassword":"newPass"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('joe@newEmail.com', $jsend->data->user->email);
    }

    public function test_422_response_is_returned_if_user_email_is_already_taken_by_another_user()
    {
        $this->createAdditionalUser();

        $request = new ServerRequest(
            'post',
            '/api/v1/user/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"93449e9d-4082-4305-8840-fa1673bcf915","email":"andrea@mail.com","password":"password", "newPassword":""}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('A user has already registered with this email address', $jsend->data->errors[0]->message);
    }

    public function test_401_response_is_returned_if_old_password_does_not_match_password_stored_for_user()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/user/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"93449e9d-4082-4305-8840-fa1673bcf915","email":"joe@email.com","password":"wrongPassword", "newPassword":"newPass"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unable to process request - please try again', $jsend->data->errors[0]->message);
    }

    public function test_422_response_is_returned_if_specific_required_fields_are_missing()
    {
        $request = new ServerRequest(
            'post',
            '/api/v1/user/update',
            ['AuthorizationToken' => (string) $this->token->getToken(), 'AuthenticationToken' => (string) $this->user->getId()],
            '{"user_id":"93449e9d-4082-4305-8840-fa1673bcf915","email":"joe@email.com"}'

        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('fail', $jsend->status);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertCount(1, $jsend->data->errors);
        $this->assertEquals("Required field 'password' is missing", $jsend->data->errors[0]->message);
    }

    private function createAdditionalUser()
    {
        $this->orchestrator->createUser(
            (new User('24306b5d-9107-4c26-bd55-d0ff6ac9382a'))
                ->setEmail('andrea@mail.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
        );
    }
}
