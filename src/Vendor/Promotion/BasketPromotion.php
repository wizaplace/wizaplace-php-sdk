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

/**
 * Class BasketPromotion
 * @package Wizaplace\SDK\Vendor\Promotion
 */
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
        $this->companyId = to_int($data['company_id']);
        $this->name = to_string($data['name']);
        $this->active = (bool) $data['active'];
        $this->rule = self::denormalizeRule($data['rule']);
        $this->discounts = array_map([self::class, 'denormalizeDiscount'], $data['discounts']);
        $this->period = self::denormalizePeriod($data['period']);
        $this->coupon = isset($data['coupon']) ? to_string($data['coupon']) : null;
    }

    /**
     * @return string
     */
    public function getPromotionId(): string
    {
        return $this->promotionId;
    }

    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return BasketRule
     */
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

    /**
     * @return PromotionPeriod
     */
    public function getPeriod(): PromotionPeriod
    {
        return $this->period;
    }

    /**
     * @return string|null
     */
    public function getCoupon(): ?string
    {
        return $this->coupon;
    }

    /**
     * @inheritdoc
     *
     * @return array
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

    /**
     * @param array $discountData
     *
     * @return Discount
     * @throws \Exception
     */
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

    /**
     * @param array $periodData
     *
     * @return PromotionPeriod
     * @throws \Exception
     */
    private static function denormalizePeriod(array $periodData): PromotionPeriod
    {
        return new PromotionPeriod(
            \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $periodData['from']),
            \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $periodData['to'])
        );
    }

    /**
     * @param array $ruleData
     *
     * @return BasketRule
     * @throws \Exception
     */
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
