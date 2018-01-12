<?php

namespace Cocktales\Application\Http\Api\v1\Validation\Profile;

use Cocktales\Framework\JsendResponse\JsendError;
use PHPUnit\Framework\TestCase;

class ProfileRequestValidatorTest extends TestCase
{
    /** @var ProfileRequestValidator   */
    private $validator;

    public function setUp()
    {
        $this->validator = new ProfileRequestValidator;
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
        $this->assertEquals(new JsendError("Required field 'username' is missing"), $errors[1]);
    }

    public function correctRequests()
    {
        return [
            [
                'create',
                (object) [
                    'user_id' => 'f530caab-1767-4f0c-a669-331a7bf0fc85',
                    'username' => 'Big Boy'
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
                    'username' => 'Big Boy'
                ]
            ]
        ];
    }
}
