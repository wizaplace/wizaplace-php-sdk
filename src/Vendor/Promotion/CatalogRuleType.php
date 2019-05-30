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
 * Class MaxUsageCountRule
 * @package Wizaplace\SDK\Vendor\Promotion
 *
 * @internal for serialization purposes only
 * @method static CatalogRuleType AND()
 * @method static CatalogRuleType OR()
 * @method static CatalogRuleType PRODUCT_IN_LIST()
 * @method static CatalogRuleType PRODUCT_IN_CATEGORY_LIST()
 * @method static CatalogRuleType PRODUCT_PRICE_SUPERIOR_TO()
 * @method static CatalogRuleType PRODUCT_PRICE_INFERIOR_TO()
 */
final class CatalogRuleType extends Enum implements NormalizableInterface
{
    private const AND = 'and';
    private const OR = 'or';
    private const PRODUCT_IN_LIST = 'product_in_list';
    private const PRODUCT_IN_CATEGORY_LIST = 'product_in_category_list';
    private const PRODUCT_PRICE_SUPERIOR_TO = 'product_price_superior_to';
    private const PRODUCT_PRICE_INFERIOR_TO = 'product_price_inferior_to';

    /**
     * @inheritdoc
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array()): string
    {
        return $this->getValue();
    }
}
