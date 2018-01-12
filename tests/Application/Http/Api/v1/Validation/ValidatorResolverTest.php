<?php

namespace Cocktales\Application\Http\Api\v1\Validation;

use Cocktales\Application\Http\Api\v1\Validation\Avatar\AvatarRequestValidator;
use Cocktales\Application\Http\Api\v1\Validation\Cocktail\CocktailRequestValidator;
use Cocktales\Application\Http\Api\v1\Validation\CocktailImage\CocktailImageRequestValidatorTest;
use Cocktales\Application\Http\Api\v1\Validation\Profile\ProfileRequestValidator;
use Cocktales\Application\Http\Api\v1\Validation\User\UserRequestValidator;
use Cocktales\Framework\Exception\RequestValidationException;
use Cocktales\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class ValidatorResolverTest extends TestCase
{
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  ValidatorResolver */
    private $resolver;

    public function setUp()
    {
        $this->container = $this->createContainer();
        $this->resolver = new ValidatorResolver($this->container);
    }

    /**
     * @param $entity
     * @param $class
     * @dataProvider validAssertions()
     */
    public function test_correct_classes_are_resolved($entity, $class)
    {
        $this->assertInstanceOf($class, $this->resolver->resolve($entity));
    }

    public function test_exception_is_thrown_if_class_cannot_be_resolved()
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage("'wrong' does not link to a valid path");
        $this->resolver->resolve('wrong');
    }

    public function validAssertions()
    {
        return [
            [
                'avatar',
                AvatarRequestValidator::class
            ],
            [
                'cocktail',
                CocktailRequestValidator::class
            ],
            [
                'cocktail-image',
                CocktailImageRequestValidatorTest::class
            ],
            [
                'profile',
                ProfileRequestValidator::class
            ],
            [
                'user',
                UserRequestValidator::class
            ]
        ];
    }
}
