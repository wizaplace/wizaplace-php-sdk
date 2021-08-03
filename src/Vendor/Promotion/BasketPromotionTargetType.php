<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

use MyCLabs\Enum\Enum;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class BasketPromotionTargetType
 * @package Wizaplace\SDK\Vendor\Promotion
 *
 * @internal for serialization purposes only
 * @method static BasketPromotionTargetType BASKET()
 * @method static BasketPromotionTargetType PRODUCTS()
 * @method static BasketPromotionTargetType PRODUCT_CATEGORY()
 * @method static BasketPromotionTargetType SHIPPING()
 */
final class BasketPromotionTargetType extends Enum implements NormalizableInterface
{
    private const BASKET = 'basket';
    private const PRODUCTS = 'product_in_basket';
    private const PRODUCT_CATEGORY = 'product_category_in_basket';
    private const SHIPPING = 'shipping';

    /**
     * @inheritdoc
     *
     * @param NormalizerInterface $normalizer
     * @param null                $format
     * @param array               $context
     *
     * @return string
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array()): string
    {
        return $this->getValue();
    }
}
