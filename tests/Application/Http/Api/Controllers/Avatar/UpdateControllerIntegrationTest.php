<?php

namespace Cocktales\Application\Http\Api\v1\Controllers\Avatar;

use Cocktales\Testing\Traits\RunsMigrations;
use Cocktales\Testing\Traits\UsesContainer;
use Cocktales\Testing\Traits\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\UploadedFile;
use Interop\Container\ContainerInterface;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;

class UpdateControllerIntegrationTest extends TestCase
{
    use UsesHttpServer;
    use UsesContainer;
    use RunsMigrations;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Filesystem */
    private $filesystem;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->filesystem = $this->container->get(Filesystem::class);
    }

    public function test_success_response_is_received_and_updated_avatar_is_returned()
    {
        $this->createAvatar();

        $request = (new ServerRequest('post', '/api/v1/avatar/update', [],
            '{
               "user_id":"8897fa60-e66f-41fb-86a2-9828b1785481" 
            }'
        ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.jpg', 22000, UPLOAD_ERR_OK, 'default_avatar.jpg', 'image/jpeg')
        ]);

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('8897fa60-e66f-41fb-86a2-9828b1785481.jpg', $jsend->data->avatar->filename);

        $this->deleteDirectory();
    }

    public function test_success_response_is_received_and_updated_avatar_with_new_extension_received_if_file_format_is_different_to_existing()
    {
        $this->createAvatar();

        $request = (new ServerRequest('post', '/api/v1/avatar/update', [],
            '{
               "user_id":"8897fa60-e66f-41fb-86a2-9828b1785481" 
            }'
        ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.png', 22000, UPLOAD_ERR_OK, 'default_avatar.png', 'image/png')
        ]);

        $response = $this->handle($this->container, $request);

        $jsend = json_decode($response->getBody()->getContents());

        $this->assertEquals('success', $jsend->status);
        $this->assertEquals('8897fa60-e66f-41fb-86a2-9828b1785481.png', $jsend->data->avatar->filename);

        $this->deleteDirectory();
    }

    private function createAvatar()
    {
        $request = (new ServerRequest('post', '/api/v1/avatar/create', [],
            '{
               "user_id":"8897fa60-e66f-41fb-86a2-9828b1785481" 
            }'
        ))->withUploadedFiles([
            'avatar' => new UploadedFile('./src/public/img/default_avatar.jpg', 22000, UPLOAD_ERR_OK, 'default_avatar.jpg', 'image/jpeg')
        ]);

        $this->handle($this->container, $request);
    }

    private function deleteDirectory()
    {
        $this->filesystem->deleteDir('/avatar');
    }
}
