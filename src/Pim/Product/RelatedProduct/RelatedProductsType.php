<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\RelatedProduct;

use MyCLabs\Enum\Enum;

/**
 * Class RelatedProductType
 * @package Wizaplace\SDK\RelatedProdct
 *
 * @internal for serialization purposes only
 * @method static RelatedProductsType RECOMMENDED()
 * @method static RelatedProductsType MANDATORY()
 * @method static RelatedProductsType PAIRED()
 * @method static RelatedProductsType SIMILAR()
 * @method static RelatedProductsType BUNDLE()
 * @method static RelatedProductsType ACCESSORY()
 * @method static RelatedProductsType SERVICE()
 * @method static RelatedProductsType SHIPPING()
 * @method static RelatedProductsType OPTION()
 * @method static RelatedProductsType TIERS()
 * @method static RelatedProductsType OTHER()
 */
class RelatedProductsType extends Enum
{
    public const RECOMMENDED = 'recommended';
    public const MANDATORY = 'mandatory';
    public const PAIRED = 'paired';
    public const SIMILAR = 'similar';
    public const BUNDLE = 'bundle';
    public const ACCESSORY = 'accessory';
    public const SERVICE = 'service';
    public const SHIPPING = 'shipping';
    public const OPTION = 'option';
    public const TIERS = 'tiers';
    public const OTHER = 'other';
}
