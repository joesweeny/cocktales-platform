<?php

namespace Cocktales\Domain\Ingredient\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Type BRANDY()
 * @method static Type CACHACA()
 * @method static Type GIN()
 * @method static Type RUM()
 * @method static Type SCHNAPPS()
 * @method static Type TEQUILA()
 * @method static Type VODKA()
 * @method static Type WHISKEY()
 *
 * @method static Type BERRY_LIQUEUR()
 * @method static Type CHOCOLATE_LIQUEUR()
 * @method static Type COFFEE_LIQUEUR()
 * @method static Type CREAM_LIQUEUR()
 * @method static Type FLOWER_LIQUEUR()
 * @method static Type FRUIT_LIQUEUR()
 * @method static Type HERBAL_LIQUEUR()
 * @method static Type HONEY_LIQUEUR()
 * @method static Type NUT_LIQUEUR()
 * @method static Type WHISKEY_LIQUEUR()
 * @method static Type OTHER_LIQUEUR()
 *
 * @method static Type CHAMPAGNE()
 *
 * @method static Type RED_WINE()
 * @method static Type PORT()
 * @method static Type WHITE_WINE()
 * @method static Type ROSE_WINE()
 * @method static Type VERMOUTH()
 * @method static Type SHERRY()
 *
 * @method static Type LAGER()
 * @method static Type ALE()
 *
 * @method static Type APPLE_CIDER()
 * @method static Type BERRY_CIDER()
 * @method static Type MIXED_FRUIT_CIDER()
 *
 * @method static Type SOFT_DRINK()
 * @method static Type FRUIT_JUICE()
 * @method static Type BITTERS()
 * @method static Type SYRUP()
 * @method static Type MIX()
 * @method static Type ALCOPOP()
 *
 * @method static Type GARNISH()
 */
class Type extends Enum
{
    const BRANDY = 'Brandy';
    const CACHACA = 'Cachaça';
    const GIN = 'Gin';
    const RUM = 'Rum';
    const SCHNAPPS = 'Schnapps';
    const TEQUILA = 'Tequila';
    const VODKA = 'Vodka';
    const WHISKEY = 'Whiskey';

    const BERRY_LIQUEUR = 'Berry Liqueur';
    const CHOCOLATE_LIQUEUR = 'Chocolate Liqueur';
    const COFFEE_LIQUEUR = 'Coffee Liqueur';
    const CREAM_LIQUEUR = 'Cream Liqueur';
    const FLOWER_LIQUEUR = 'Flower Liqueur';
    const FRUIT_LIQUEUR = 'Fruit Liqueur';
    const HERBAL_LIQUEUR = 'Herbal Liqueur';
    const HONEY_LIQUEUR = 'Honey Liqueur';
    const NUT_LIQUEUR = 'Nut Liqueur';
    const WHISKEY_LIQUEUR = 'Whiskey Liqueur';
    const OTHER_LIQUEUR = 'Other Liqueur';

    const CHAMPAGNE = 'Champagne';

    const RED_WINE = 'Red Wine';
    const PORT = 'Port';
    const WHITE_WINE = 'White Wine';
    const ROSE_WINE = 'Rose Wine';
    const VERMOUTH = 'Vermouth';
    const SHERRY = 'Sherry';

    const LAGER = 'Lager';
    const ALE = 'Ale';

    const APPLE_CIDER = 'Apple Cider';
    const BERRY_CIDER = 'Berry Cider';
    const MIXED_FRUIT_CIDER = 'Mixed Fruit Cider';

    const SOFT_DRINK = 'Soft Drink';
    const FRUIT_JUICE = 'Fruit Juice';
    const BITTERS = 'Bitters';
    const SYRUP = 'Syrup';
    const MIX = 'Mix';
    const ALCOPOP = 'Alcopop';

    const GARNISH = 'Garnish';
}
