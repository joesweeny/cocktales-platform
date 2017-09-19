<?php

namespace Cocktales\Bootstrap;

use Aws\S3\S3Client;
use Chief\Busses\SynchronousCommandBus;
use Chief\Container;
use Chief\Resolvers\NativeCommandHandlerResolver;
use Cocktales\Application\Http\App\Routing\RouteManager;
use Cocktales\Domain\Avatar\Persistence\IlluminateDbAvatarRepository;
use Cocktales\Domain\Cocktail\Persistence\IlluminateDbCocktailRepository;
use Cocktales\Domain\CocktailIngredient\Persistence\IlluminateDbCocktailIngredientRepository;
use Cocktales\Domain\Ingredient\Persistence\IlluminateDbIngredientRepository;
use Cocktales\Domain\Instruction\Persistence\IlluminateDbInstructionRepository;
use Cocktales\Domain\Profile\Persistence\IlluminateDbProfileRepository;
use Cocktales\Domain\User\Persistence\IlluminateDbUserRepository;
use Cocktales\Domain\User\Persistence\Repository;
use Cocktales\Framework\CommandBus\CommandBus;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\DateTime\SystemClock;
use Dflydev\FigCookies\SetCookie;
use DI\ContainerBuilder;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use Interop\Container\ContainerInterface;
use Intervention\Image\ImageManager;
use Lcobucci\JWT\Parser;
use Cocktales\Framework\CommandBus\ChiefAdapter;
use Cocktales\Framework\Routing\Router;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Time\SystemCurrentTime;

class ContainerFactory
{
    /** @var Config */
    private $config;

    public function create(Config $config): ContainerInterface
    {
        $this->config = $config;

        return (new ContainerBuilder)
            ->useAutowiring(true)
            ->ignorePhpDocErrors(true)
            ->useAnnotations(false)
            ->writeProxiesToFile(false)
            ->addDefinitions($this->getDefinitions())
            ->build();
    }

    /**
     * @return array
     * @throws \UnexpectedValueException
     */
    protected function getDefinitions(): array
    {
        return array_merge(
            $this->defineConfig(),
            $this->defineFramework(),
            $this->defineDomain(),
            $this->defineConnections()
        );
    }

    protected function defineConfig(): array
    {
        return [
            Config::class => \DI\factory(function () {
                return $this->config;
            }),
        ];
    }

    /**
     * @return array
     * @throws \UnexpectedValueException
     */
    private function defineFramework(): array
    {
        return [
            ContainerInterface::class => \DI\factory(function (ContainerInterface $container) {
                return $container;
            }),

            Router::class => \DI\decorate(function (Router $router, ContainerInterface $container) {
                // @todo Add RouteManagers here
                return $router
                    ->addRoutes($container->get(RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\User\RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\Profile\RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\Avatar\RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\Ingredient\RouteManager::class))
                    ->addRoutes($container->get(\Cocktales\Application\Http\Api\v1\Routing\Cocktail\RouteManager::class));
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
            }),


            Clock::class => \DI\object(SystemClock::class),

            ImageManager::class => \DI\factory(function (ContainerInterface $container) {
                return new ImageManager(['driver' => 'imagick']);
            })
            
        ];
    }

    /**
     * @return array
     * @throws \LogicException
     */
    private function defineDomain(): array
    {
        return [
            Repository::class => \DI\object(IlluminateDbUserRepository::class),

            \Cocktales\Domain\Profile\Persistence\Repository::class => \DI\object(IlluminateDbProfileRepository::class),

            \Cocktales\Domain\Avatar\Persistence\Repository::class => \DI\object(IlluminateDbAvatarRepository::class),

            \Cocktales\Domain\Ingredient\Persistence\Repository::class => \DI\object(IlluminateDbIngredientRepository::class),

            \Cocktales\Domain\Instruction\Persistence\Repository::class => \DI\object(IlluminateDbInstructionRepository::class),

            \Cocktales\Domain\CocktailIngredient\Persistence\Repository::class => \DI\object(IlluminateDbCocktailIngredientRepository::class),

            \Cocktales\Domain\Cocktail\Persistence\Repository::class => \DI\object(IlluminateDbCocktailRepository::class),

            Filesystem::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);

                if ($config->get('aws.filesystem_enabled')) {
                    $client = S3Client::factory([
                        'credentials' => [
                            'key'    => $config->get('aws.key'),
                            'secret' => $config->get('aws.secret'),
                        ],
                        'region' => 'eu-west-2',
                        'version' => 'latest',
                    ]);

                    $adapter = new AwsS3Adapter($client, 'test.cocktales.io');

                    return new Filesystem($adapter);
                }

                return new Filesystem(
                    new Local('./src/public/img',
                    0, Local::SKIP_LINKS, [
                        'file' => [
                            'public' => 0777,
                            'private' => 0777,
                        ],
                        'dir' => [
                            'public' => 0777,
                            'private' => 0777,
                        ]
                ]));
            })
        ];
    }


    private function defineConnections()
    {
        return [
            AbstractSchemaManager::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(Connection::class)->getDoctrineSchemaManager();
            }),

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

}
