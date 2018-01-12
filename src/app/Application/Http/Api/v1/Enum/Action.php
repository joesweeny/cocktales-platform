<?php

namespace Cocktales\Application\Http\Api\v1\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Action CREATE()
 * @method static Action GET()
 * @method static Action GET_BY_ID()
 * @method static Action UPDATE()
 * @method static Action REGISTER()
 * @method static Action LOGIN()
 * @method static Action GET_BY_INGREDIENTS()
 * @method static Action GET_BY_USER()
 *
 */
class Action extends Enum
{
    const CREATE = 'create';
    const GET = 'get';
    const GET_BY_ID = 'get-by-id';
    const UPDATE = 'update';
    const REGISTER = 'register';
    const LOGIN = 'login';
    const GET_BY_INGREDIENTS = 'get-by-ingredients';
    const GET_BY_USER = 'get-by-user';
}
