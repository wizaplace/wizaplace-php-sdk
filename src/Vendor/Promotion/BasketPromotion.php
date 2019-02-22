<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
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
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceInferiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceSuperiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountPerUserRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\MaxUsageCountRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\OrBasketRule;
use function theodorejb\polycast\to_float;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketPromotionTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\ProductsTarget;
use Wizaplace\SDK\Vendor\Promotion\Targets\ShippingTarget;

final class BasketPromotion implements \JsonSerializable
{
    /** @var string */
    private $promotionId;

    /** @var int */
    private $companyId;

    /** @var string */
    private $name;

    /** @var bool */
    private $active;

    /** @var BasketRule */
    private $rule;

    /** @var Discount[] */
    private $discounts;

    /** @var PromotionPeriod */
    private $period;

    /** @var null|string */
    private $coupon;

    /** @var BasketPromotionTarget */
    private $target;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->promotionId = to_string($data['promotion_id']);
        $this->companyId = to_int($data['company_id']);
        $this->name = to_string($data['name']);
        $this->active = (bool) $data['active'];
        $this->rule = self::denormalizeRule($data['rule']);
        $this->discounts = array_map([self::class, 'denormalizeDiscount'], $data['discounts']);
        $this->period = self::denormalizePeriod($data['period']);
        $this->coupon = isset($data['coupon']) ? to_string($data['coupon']) : null;
        $this->target = self::denormalizeTarget($data['target']);
    }

    public function getPromotionId(): string
    {
        return $this->promotionId;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getRule(): BasketRule
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

    public function getCoupon(): ?string
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
        $serializer = new Serializer([
            new DateTimeNormalizer(\DateTime::RFC3339),
            new CustomNormalizer(),
            new GetSetMethodNormalizer(null, new CamelCaseToSnakeCaseNameConverter()),
        ]);

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
        $target = explode(';', $targetData['type']);
        $type = $target[0];

        $type = new BasketPromotionTargetType($type);

        switch (true) {
            case BasketPromotionTargetType::BASKET()->equals($type):
                return new BasketTarget();

            case BasketPromotionTargetType::PRODUCTS()->equals($type):
                // We have to format products_ids data for ProductTarget constructor
                if (is_string($target[1]) && $target[1] !== "") {
                    $targetData['products_ids'] = explode(',', $target[1]);
                    $targetData['products_ids'] = array_map(
                        function (string $id):int {
                            return (int) $id;
                        },
                        $targetData['products_ids']
                    );
                } else {
                    throw new \Exception('Empty target product ids');
                }

                return new ProductsTarget(...$targetData['products_ids']);

            case BasketPromotionTargetType::SHIPPING()->equals($type):
                return new ShippingTarget();

            default:
                throw new \Exception('Unexpected target type');
        }
    }

    private static function denormalizeRule(array $ruleData): BasketRule
    {
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
            default:
                throw new \Exception('unexpected rule type');
        }
    }
}
