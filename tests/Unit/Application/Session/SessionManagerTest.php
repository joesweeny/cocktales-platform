<?php

namespace Cocktales\Application\Session;

use Cocktales\Helpers\CreatesContainer;
use Cocktales\Helpers\UsesHttpServer;
use GuzzleHttp\Psr7\ServerRequest;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use PSR7Session\Http\SessionMiddleware;
use Zend\Diactoros\Response;

class SessionManagerTest extends TestCase
{
    use UsesHttpServer;
    use CreatesContainer;

    /** @var  ContainerInterface */
    private $container;

    public function setUp()
    {
        $this->container = $this->createContainer();
    }

    public function test_attributes_can_be_set_on_session()
    {
        $middleware = $this->container->get(SessionMiddleware::class);

        $request = new ServerRequest('get', '/');
        $response = new Response();
        $middleware->__invoke($request, $response, function (ServerRequest $request, ResponseInterface $response) {

            $manager = $this->container->get(SessionManager::class);

            $manager->set($request , 'key', 'value');

            $this->assertEquals('value', $manager->get($request, 'key'));

            return $response;
        });
    }

    public function test_session_value_can_be_retrieved_using_set_cookie_attribute()
    {
        $middleware = $this->container->get(SessionMiddleware::class);
        $manager = $this->container->get(SessionManager::class);

        $request = new ServerRequest('get', '/');
        $response = new Response();
        $response = $middleware->__invoke($request, $response, function (ServerRequest $request, ResponseInterface $response) use ($manager) {

            $manager->set($request , 'key', 'value');

            $this->assertEquals('value', $manager->get($request, 'key'));

            return $response;
        });

        $setcookie = $response->getHeader('Set-Cookie')[0];

        $firstEquals = strpos($setcookie, '=', 0);

        $cookieKey = substr($setcookie, 0, $firstEquals);
        $cookieVal = substr($setcookie, $firstEquals+1);


        $firstSemiColon = strpos($cookieVal, ';', 0);
        $cookieVal = substr($cookieVal, 0, $firstSemiColon);


        $request = new ServerRequest('get', '/');

        $request = $request->withCookieParams([
            $cookieKey => $cookieVal
        ]);

        $middleware->__invoke($request, $response, function (ServerRequest $request, ResponseInterface $response) use ($manager) {

            $this->assertEquals('value', $manager->get($request, 'key'));

            return $response;
        });

    }

    public function test_attributes_are_deleted_when_session_is_destroyed()
    {
        $middleware = $this->container->get(SessionMiddleware::class);

        $request = new ServerRequest('get', '/');
        $response = new Response();
        $middleware->__invoke($request, $response, function (ServerRequest $request, ResponseInterface $response) {

            $manager = $this->container->get(SessionManager::class);

            $manager->set($request , 'key', 'value');

            $this->assertEquals('value', $manager->get($request, 'key'));

            $manager->destroy($request);

            $this->assertNull($manager->get($request, 'key'));

            return $response;
        });
    }
}
