<?php

namespace Cocktales\Application\Http\Api\v1\Validation\Avatar;

use Cocktales\Framework\JsendResponse\JsendError;
use PHPUnit\Framework\TestCase;

class AvatarRequestValidatorTest extends TestCase
{
    /** @var  AvatarRequestValidator */
    private $validator;

    public function setUp()
    {
        $this->validator = new AvatarRequestValidator;
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
        $this->assertEquals("Required field 'image' is missing", $errors[1]);
        $this->assertEquals("Required field 'format' is missing", $errors[2]);
    }

    public function correctRequests()
    {
        return [
            [
                'create',
                (object) [
                    'user_id' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
                    'image' => 'Image string',
                    'format' => 'base64'
                ]
            ],
            [
                'get',
                (object) [
                    'user_id' => 'f530caab-1767-4f0c-a669-331a7bf0fc85'
                ]
            ],
            [
                'update',
                (object) [
                    'user_id' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
                    'image' => 'Image string',
                    'format' => 'base64'
                ]
            ]
        ];
    }
}
