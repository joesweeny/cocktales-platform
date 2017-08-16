<?php

namespace Cocktales\Domain\Ingredient\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Category SPIRIT()
 * @method static Category LIQUEUR()
 * @method static Category WINE()
 * @method static Category CHAMPAGNE()
 * @method static Category MIXER()
 * @method static Category GARNISH()
 */
class Category extends Enum
{
    const SPIRIT = 'SPIRIT';
    const LIQUEUR = 'LIQUEUR';
    const WINE = 'WINE';
    const CHAMPAGNE = 'CHAMPAGNE';
    const MIXER = 'MIXER';
    const GARNISH = 'GARNISH';
}
