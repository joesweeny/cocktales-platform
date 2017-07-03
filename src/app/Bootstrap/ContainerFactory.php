<?php

namespace Cocktales\Bootstrap;

use Chief\Busses\SynchronousCommandBus;
use Chief\Container;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Cocktales\Application\Http\Middleware\PathGuard;
use Cocktales\Application\Http\Router;
use Cocktales\Application\Http\Api\v1\Routing\Home\RouteManager;
use Cocktales\Application\Http\Session\SessionAuthenticator;
use Cocktales\Domain\Profile\Persistence\IlluminateDbProfileRepository;
use Cocktales\Domain\User\Persistence\IlluminateDbUserRepository;
use Cocktales\Domain\User\Persistence\Repository;
use Cocktales\Framework\CommandBus\ChiefAdapter;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\DateTime\SystemClock;
use Dflydev\FigCookies\SetCookie;
use DI\ContainerBuilder;
use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use Interop\Container\ContainerInterface;
use Lcobucci\JWT\Parser;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Time\SystemCurrentTime;
use Twig_Environment;
use Twig_Loader_Filesystem;

class ContainerFactory
{
    /**
     * @return ContainerInterface
     * @throws \InvalidArgumentException
     */
    public function create(): ContainerInterface
    {
        return (new ContainerBuilder)
            ->useAutowiring(true)
            ->ignorePhpDocErrors(true)
            ->useAnnotations(false)
            ->writeProxiesToFile(false)
            ->addDefinitions($this->getDefinitions())
            ->build();
    }

    protected function getDefinitions()
    {
        return array_merge(
            $this->defineConfig(),
            $this->defineConnections(),
            $this->defineFramework(),
            $this->definePersistenceLayer(),
            $this->defineFrameworkLayer()
        );
    }

    protected function defineConfig()
    {
        return [
            Config::class => \DI\factory(function () {
                return ConfigFactory::create();
            })
        ];
    }

    private function defineFramework()
    {
        return [
            ContainerInterface::class => \DI\factory(function (ContainerInterface $container) {
                return $container;
            }),

            Twig_Environment::class => \DI\factory(function (ContainerInterface $container) {
                return new Twig_Environment(
                    new Twig_Loader_Filesystem(ConfigFactory::fromContainer($container)->get('app.templates'))
                );
            }),

            Router::class => \DI\decorate(function (Router $router, ContainerInterface $container) {
                return $router
                    ->addRoutes($container->get(RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\Welcome\RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\User\RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\Profile\RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\Auth\RouteManager::class));

            }),

            PathGuard::class => \DI\factory(function (ContainerInterface $container) {
                return new PathGuard($container->get(SessionAuthenticator::class), "/^\/auth\/login/");
            }),

            CommandBus::class => \DI\factory(function (ContainerInterface $container) {
                $bus = new ChiefAdapter(new SynchronousCommandBus(new NativeCommandHandlerResolver(new class($container) implements Container {
                    /**
                     * @var ContainerInterface
                     */
                    private $container;
                    public function __construct(ContainerInterface $container)
                    {
                        $this->container = $container;
                    }
                    public function make($class)
                    {
                        return $this->container->get($class);
                    }
                })));
//                $bus->pushDecorator($container->get(AuthDecorator::class));
                return $bus;
            }),

            SessionMiddleware::class => \DI\factory(function (ContainerInterface $container) {

                return new SessionMiddleware(
                    new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                    'OpcMuKmoxVhzW0Y1iESpjWwL/D3UBdDauJOe742BJ5Q=',
                    'OpcMuKmoxVhzW0Y1iESpjWwL/D3UBdDauJOe742BJ5Q=',
                    SetCookie::create(SessionMiddleware::DEFAULT_COOKIE)
                        ->withSecure(false)
                        ->withHttpOnly(true)
                        ->withPath('/'),
                    new Parser(),
                    1200,
                    new SystemCurrentTime()
                );
            })
        ];
    }

    private function defineConnections()
    {
        return [
            Connection::class => \DI\factory(function (ContainerInterface $container) {

                $config = $container->get(Config::class);

                $dsn = $config->get('database.default.pdo.dsn');

                if (substr($dsn, 0, 5) === 'mysql') {
                    return new MySqlConnection($container->get(\PDO::class));
                }

                if (substr($dsn, 0, 6) === 'sqlite') {
                    return new SQLiteConnection($container->get(\PDO::class));
                }

                throw new \RuntimeException("Unrecognised DNS {$dsn}");
            }),

            \Doctrine\DBAL\Driver\Connection::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(Connection::class)->getDoctrineConnection();
            }),

            \PDO::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);
                $pdo = new \PDO(
                    $config->get('database.default.pdo.dsn'),
                    $config->get('database.default.pdo.user'),
                    $config->get('database.default.pdo.password')
                );
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return $pdo;
            }),
        ];
    }

    private function definePersistenceLayer()
    {
        return [
            Repository::class => \DI\object(IlluminateDbUserRepository::class),
            \Cocktales\Domain\Profile\Persistence\Repository::class => \DI\object(IlluminateDbProfileRepository::class)
        ];
    }

    private function defineFrameworkLayer()
    {
        return [
            Clock::class => \DI\object(SystemClock::class)
        ];
    }
}
