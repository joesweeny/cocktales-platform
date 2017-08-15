<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Domain\Avatar\AvatarOrchestrator;
use Cocktales\Domain\Avatar\Entity\Avatar;
use Cocktales\Framework\Uuid\Uuid;
use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Cocktales\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class GetControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  AvatarOrchestrator */
    private $orchestrator;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->orchestrator = $this->container->get(AvatarOrchestrator::class);
    }

    public function test_success_response_is_received_and_avatar_information_returned()
    {
        $this->orchestrator->createAvatar((new Avatar)
            ->setUserId(new Uuid('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setFilename('filename.jpg'));

        $request = new ServerRequest('GET', '/api/v1/avatar/get', [],
            '{"user_id" : "dc5b6421-d452-4862-b741-d43383c3fe1d"}'
        );

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('filename.jpg', $jsend->data->avatar->filename);
    }
}
