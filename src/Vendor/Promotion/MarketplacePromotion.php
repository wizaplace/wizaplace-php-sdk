<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Wizaplace\SDK\Vendor\Promotion\Discounts\Discount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\FixedDiscount;
use Wizaplace\SDK\Vendor\Promotion\Discounts\PercentageDiscount;
use Wizaplace\SDK\Vendor\Promotion\Rules\AndBasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketHasProductInListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceInferiorOrEqualToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceInferiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceSuperiorOrEqualToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceSuperiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountPerUserRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\OrBasketRule;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketPromotionTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\ProductsTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\ShippingTarget;

use function theodorejb\polycast\to_string;
use function theodorejb\polycast\to_float;

final class MarketplacePromotion implements \JsonSerializable
{
    /** @var string */
    private $promotionId;

    /** @var string */
    private $name;

    /** @var bool */
    private $active;

    /** @var bool */
    private $isValid;

    /** @var BasketRule */
    private $rule;

    /** @var Discount[] */
    private $discounts;

    /** @var PromotionPeriod */
    private $period;

    /** @var string */
    private $coupon;

    /** @var BasketPromotionTarget */
    private $target;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->promotionId = to_string($data['promotion_id']);
        $this->name = to_string($data['name']);
        $this->active = (bool) $data['active'];
        $this->isValid = \array_key_exists('isValid', $data) ? (bool) $data['isValid'] : false;
        $this->rule = self::denormalizeRule($data['rule']);
        $this->discounts = array_map([self::class, 'denormalizeDiscount'], $data['discounts']);
        $this->period = self::denormalizePeriod($data['period']);
        $this->coupon = to_string($data['coupon']);
        $this->target = self::denormalizeTarget($data['target']);
    }

    public function getPromotionId(): string
    {
        return $this->promotionId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function getRule(): ?BasketRule
    {
        return $this->rule;
    }

    /**
     * @return Discount[]
     */
    public function getDiscounts(): array
    {
        return $this->discounts;
    }

    public function getPeriod(): PromotionPeriod
    {
        return $this->period;
    }

    public function getCoupon(): string
    {
        return $this->coupon;
    }

    /**
     * @return BasketPromotionTarget|null
     */
    public function getTarget(): ?BasketPromotionTarget
    {
        return $this->target;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        $serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new CustomNormalizer(),
                new GetSetMethodNormalizer(null, new CamelCaseToSnakeCaseNameConverter()),
            ]
        );

        return $serializer->normalize($this);
    }

    private static function denormalizeDiscount(array $discountData): Discount
    {
        $type = new DiscountType($discountData['type']);

        switch (true) {
            case DiscountType::PERCENTAGE()->equals($type):
                return new PercentageDiscount(to_float($discountData['percentage']));
            case DiscountType::FIXED()->equals($type):
                return new FixedDiscount(to_float($discountData['value']));
            default:
                throw new \Exception('unexpected discount type');
        }
    }

    private static function denormalizePeriod(array $periodData): PromotionPeriod
    {
        return new PromotionPeriod(
            \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $periodData['from']),
            \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $periodData['to'])
        );
    }

    private static function denormalizeTarget(array $targetData): BasketPromotionTarget
    {
        // We have to explode 'type' property because product_ids are serialized in it :(
        if (\array_key_exists('type', $targetData) === false || \is_string($targetData['type']) === false) {
            throw new \Exception('Target type is empty');
        }

        $target = explode(';', $targetData['type']);
        $type = new BasketPromotionTargetType($target[0]);

        switch (true) {
            case BasketPromotionTargetType::BASKET()->equals($type):
                return new BasketTarget();

            case BasketPromotionTargetType::PRODUCTS()->equals($type):
                // We have to format products_ids data for ProductTarget constructor
                if (isset($target[1]) && \is_string($target[1]) && $target[1] !== "") {
                    $targetData['products_ids'] = array_map(
                        function (string $id): int {
                            return (int) $id;
                        },
                        explode(',', $target[1])
                    );
                }

                if (\array_key_exists('products_ids', $targetData) === false
                    || \is_array($targetData['products_ids']) === false
                    || \count($targetData['products_ids']) === 0
                ) {
                    throw new \Exception('Empty target products ids');
                }

                return new ProductsTarget(...$targetData['products_ids']);

            case BasketPromotionTargetType::SHIPPING()->equals($type):
                return new ShippingTarget();

            default:
                throw new \Exception('Unexpected target type');
        }
    }

    private static function denormalizeRule(?array $ruleData): ?BasketRule
    {
        if ($ruleData === null) {
            return null;
        }

        $type = new BasketRuleType($ruleData['type']);

        switch (true) {
            case BasketRuleType::OR()->equals($type):
                return new OrBasketRule(...array_map([self::class, 'denormalizeRule'], $ruleData['items']));
            case BasketRuleType::AND()->equals($type):
                return new AndBasketRule(...array_map([self::class, 'denormalizeRule'], $ruleData['items']));
            case BasketRuleType::BASKET_HAS_PRODUCT_IN_LIST()->equals($type):
                return new BasketHasProductInListRule(...$ruleData['products_ids']);
            case BasketRuleType::MAX_USAGE_COUNT()->equals($type):
                return new MaxUsageCountRule($ruleData['value']);
            case BasketRuleType::MAX_USAGE_COUNT_PER_USER()->equals($type):
                return new MaxUsageCountPerUserRule($ruleData['value']);
            case BasketRuleType::BASKET_PRICE_INFERIOR_TO()->equals($type):
                return new BasketPriceInferiorToRule(to_float($ruleData['value']));
            case BasketRuleType::BASKET_PRICE_SUPERIOR_TO()->equals($type):
                return new BasketPriceSuperiorToRule(to_float($ruleData['value']));
            case BasketRuleType::BASKET_PRICE_INFERIOR_OR_EQUAL_TO()->equals($type):
                return new BasketPriceInferiorOrEqualToRule(to_float($ruleData['value']));
            case BasketRuleType::BASKET_PRICE_SUPERIOR_OR_EQUAL_TO()->equals($type):
                return new BasketPriceSuperiorOrEqualToRule(to_float($ruleData['value']));
            default:
                throw new \Exception('unexpected rule type');
        }
    }
}
