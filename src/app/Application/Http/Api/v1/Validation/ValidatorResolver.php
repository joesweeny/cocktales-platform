<?php

namespace Cocktales\Application\Http\Api\v1\Validation;

use Cocktales\Application\Http\Api\v1\Validation\Avatar\AvatarRequestValidator;
use Cocktales\Application\Http\Api\v1\Validation\Cocktail\CocktailRequestValidator;
use Cocktales\Application\Http\Api\v1\Validation\CocktailImage\CocktailImageRequestValidator;
use Cocktales\Application\Http\Api\v1\Validation\Profile\ProfileRequestValidator;
use Cocktales\Application\Http\Api\v1\Validation\User\UserRequestValidator;
use Cocktales\Framework\Exception\RequestValidationException;
use Interop\Container\ContainerInterface;

class ValidatorResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function resolve(string $entity): RequestBodyValidator
    {
        switch ($entity) {
            case 'avatar':
                return $this->container->get(AvatarRequestValidator::class);
            case 'cocktail':
                return $this->container->get(CocktailRequestValidator::class);
            case 'cocktail-image':
                return $this->container->get(CocktailImageRequestValidator::class);
            case 'profile':
                return $this->container->get(ProfileRequestValidator::class);
            case 'user':
                return $this->container->get(UserRequestValidator::class);
            default:
                throw new RequestValidationException("'{$entity}' does not link to a valid path");
        }
    }
}
