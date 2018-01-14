<?php

namespace Cocktales\Bootstrap;

use Interop\Container\ContainerInterface;

class ConfigFactory
{
    /**
     * @return Config
     */
    public static function create(): Config
    {
        return new Config([
            'base-uri' => self::fromEnv('BASE_URI', 'cocktales.io'),

            'database' => [
                'default' => [
                    'pdo' => [
                        'dsn' => self::fromEnv('DB_DSN'),
                        'user' => self::fromEnv('DB_USER'),
                        'password' => self::fromEnv('DB_PASS'),
                    ]
                ]
            ],

            'aws' => [
                'filesystem_enabled' => self::fromEnv('AWS_FILESYSTEM_ENABLED', false) === 'true',

                'key' => self::fromEnv('AWS_ACCESS_KEY_ID'),

                'secret' => self::fromEnv('AWS_SECRET_KEY')
            ],

            'log' => [
                /**
                 * Which psr/log implementation to use. Options: monolog, null
                 */
                'logger' => self::fromEnv('LOG_LOGGER') ?: 'null'
            ]
        ]);
    }

    /**
     * Get an value from ENV.
     *
     * If the value does not exist in ENV, then search for a {key}_FILE ENV variable.. if the _FILE
     * var exists, then read the contents of that file to get the value.
     *
     * @param string $key
     * @param $default
     * @return mixed|null
     */
    private static function fromEnv(string $key, $default = null)
    {
        if ($val = getenv($key)) {
            return $val;
        }

        if ($path = getenv("{$key}_FILE")) {
            if (file_exists($path)) {
                return file_get_contents($path);
            }
        }

        return $default;
    }

    /**
     * @param ContainerInterface $container
     * @return Config
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public static function fromContainer(ContainerInterface $container): Config
    {
        return $container->get(Config::class);
    }
}
