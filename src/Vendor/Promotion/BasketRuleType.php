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
 * Class BasketRuleType
 * @package Wizaplace\SDK\Vendor\Promotion
 *
 * @internal for serialization purposes only
 * @method static BasketRuleType AND()
 * @method static BasketRuleType OR()
 * @method static BasketRuleType BASKET_HAS_PRODUCT_IN_LIST()
 * @method static BasketRuleType MAX_USAGE_COUNT()
 * @method static BasketRuleType MAX_USAGE_COUNT_PER_USER()
 * @method static BasketRuleType BASKET_PRICE_SUPERIOR_TO()
 * @method static BasketRuleType BASKET_PRICE_INFERIOR_TO()
 */
final class BasketRuleType extends Enum implements NormalizableInterface
{
    private const AND = 'and';
    private const OR = 'or';
    private const BASKET_HAS_PRODUCT_IN_LIST = 'basket_has_product_in_list';
    private const MAX_USAGE_COUNT = 'max_usage_count';
    private const MAX_USAGE_COUNT_PER_USER = 'max_usage_count_per_user';
    private const BASKET_PRICE_SUPERIOR_TO = 'basket_price_superior_to';
    private const BASKET_PRICE_INFERIOR_TO = 'basket_price_inferior_to';

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
