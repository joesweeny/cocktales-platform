<?php

namespace Cocktales\Application\Http\Api\v1\Validation\Cocktail;

use Cocktales\Framework\JsendResponse\JsendError;
use PHPUnit\Framework\TestCase;

class CocktailRequestValidatorTest extends TestCase
{
    /** @var  CocktailRequestValidator */
    private $validator;

    public function setUp()
    {
        $this->validator = new CocktailRequestValidator;
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

        $this->assertEquals(new JsendError("Required field 'user_id' is missing"), $errors[0]);
        $this->assertEquals(new JsendError("Required 'cocktail' object is missing"), $errors[1]);
        $this->assertEquals(new JsendError("Required field 'name' is missing from 'cocktail' object"), $errors[2]);
        $this->assertEquals(new JsendError("Required field 'origin' is missing from 'cocktail' object"), $errors[3]);
        $this->assertEquals(new JsendError("Required 'ingredients' object is missing"), $errors[4]);
        $this->assertEquals(new JsendError("Required 'ingredients' object is not in the correct format: array"), $errors[5]);
        $this->assertEquals(new JsendError("Required 'instructions' object is missing"), $errors[6]);
        $this->assertEquals(new JsendError("Required 'instructions' object is not in the correct format: array"), $errors[7]);
    }

    public function correctRequests()
    {
        return [
            [
                'create',
                (object) [
                    'user_id' => 'a88cffac-f628-445c-9f55-ae99a0542fe6',
                    'cocktail' => (object) [
                        'name' => 'Sex on the Beach',
                        'origin' => 'Ibiza'
                    ],
                    'ingredients' => [
                        (object) [
                            'id' => '801194b1-11d2-47ec-bf5b-38ddc4a4cd69',
                            'orderNumber' => 1,
                            'quantity' => 50,
                            'measurement' => 'ml'
                        ]
                    ],
                    'instructions' => [
                        (object) [
                            'orderNumber' => 1,
                            'text' => 'Shake well'
                        ]
                    ]
                ]
            ],
            [
                'get-by-id',
                (object) [
                    'cocktail_id' => '801194b1-11d2-47ec-bf5b-38ddc4a4cd69'
                ]
            ]
        ];
    }
}
