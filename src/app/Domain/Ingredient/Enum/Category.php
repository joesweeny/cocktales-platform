<?php

namespace Cocktales\Domain\Ingredient\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Category SPIRIT()
 * @method static Category LIQUEUR()
 * @method static Category WINE()
 * @method static Category BEER()
 * @method static Category CIDER()
 * @method static Category CHAMPAGNE()
 * @method static Category MIXER()
 * @method static Category GARNISH()
 */
class Category extends Enum
{
    const SPIRIT = 'Spirit';
    const LIQUEUR = 'Liqueur';
    const WINE = 'Wine';
    const BEER = 'Beer';
    const CIDER = 'Cider';
    const CHAMPAGNE = 'Champagne';
    const MIXER = 'Mixer';
    const GARNISH = 'Garnish';
}
