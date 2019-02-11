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
 * Class DiscountType
 * @package Wizaplace\SDK\Vendor\Promotion
 *
 * @internal for serialization purposes only
 * @method static DiscountType PERCENTAGE()
 * @method static DiscountType FIXED()
 */
final class DiscountType extends Enum implements NormalizableInterface
{
    private const PERCENTAGE = 'percentage';
    private const FIXED = 'fixed';

    /**
     * @inheritdoc
     */
    public function normalize(NormalizerInterface $normalizer, $format = null, array $context = array()): string
    {
        return $this->getValue();
    }
}
