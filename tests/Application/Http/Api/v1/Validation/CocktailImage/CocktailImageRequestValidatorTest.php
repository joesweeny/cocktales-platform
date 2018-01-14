<?php

namespace Cocktales\Application\Http\Api\v1\Validation\CocktailImage;

use Cocktales\Framework\JsendResponse\JsendError;
use PHPUnit\Framework\TestCase;

class CocktailImageRequestValidatorTest extends TestCase
{
    /** @var  CocktailImageRequestValidator */
    private $validator;

    public function setUp()
    {
        $this->validator = new CocktailImageRequestValidator;
    }

    /**
     * @param $action
     * @param $body
     * @dataProvider correctRequests()
     */
    public function test_return_array_is_empty_if_request_body_is_formatted_correctly($action, $body)
    {
        $this->assertEmpty($this->validator->validate($action, $body));
    }

    public function test_validate_returns_an_array_of_jsend_objects_if_required_fields_are_missing()
    {
        $errors = $this->validator->validate('create', (object) []);

        $this->assertEquals("Required field 'user_id' is missing", $errors[0]);
        $this->assertEquals("Required field 'cocktail_id' is missing", $errors[1]);
        $this->assertEquals("Required field 'image' is missing", $errors[2]);
        $this->assertEquals("Required field 'format' is missing", $errors[3]);
    }

    public function correctRequests()
    {
        return [
            [
                'create',
                (object) [
                    'user_id' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
                    'cocktail_id' => '054c755e-8f17-4e21-a64c-cbc8c3fbff34',
                    'image' => 'Image string',
                    'format' => 'base64'
                ]
            ],
            [
                'get',
                (object) [
                    'cocktail_id' => '054c755e-8f17-4e21-a64c-cbc8c3fbff34'
                ]
            ],
            [
                'update',
                (object) [
                    'user_id' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
                    'cocktail_id' => '054c755e-8f17-4e21-a64c-cbc8c3fbff34',
                    'image' => 'Image string',
                    'format' => 'base64'
                ]
            ]
        ];
    }
}
