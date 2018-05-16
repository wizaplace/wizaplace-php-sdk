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
 * @internal for serialization purposes only
 * @method static BasketPromotionTargetType BASKET()
 * @method static BasketPromotionTargetType PRODUCTS()
 * @method static BasketPromotionTargetType SHIPPING()
 */
final class BasketPromotionTargetType extends Enum implements NormalizableInterface
{
    private const BASKET = 'basket';
    private const PRODUCTS = 'product_in_basket';
    private const SHIPPING = 'shipping';

    /**
     * @inheritdoc
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array()): string
    {
        return $this->getValue();
    }
}
