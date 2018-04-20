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
use Wizaplace\SDK\Vendor\Promotion\Rules\AndCatalogRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\CatalogRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\OrCatalogRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductInCategoryListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductInListRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductPriceInferiorToRule;
use Wizaplace\SDK\Vendor\Promotion\Rules\ProductPriceSuperiorToRule;
use function theodorejb\polycast\to_float;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

final class CatalogPromotion implements \JsonSerializable
{
    /** @var string */
    private $promotionId;

    /** @var int */
    private $companyId;

    /** @var string */
    private $name;

    /** @var bool */
    private $active;

    /** @var CatalogRule */
    private $rule;

    /** @var Discount[] */
    private $discounts;

    /** @var PromotionPeriod */
    private $period;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->promotionId = to_string($data['promotion_id']);
        $this->companyId = to_int($data['company_id']);
        $this->name = to_string($data['name']);
        $this->active = (bool) $data['active'];
        $this->rule = self::unserializeRule($data['rule']);
        $this->discounts = array_map([self::class, 'unserializeDiscount'], $data['discounts']);
        $this->period = self::unserializePeriod($data['period']);
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

    public function getRule(): CatalogRule
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

    private static function unserializeDiscount(array $discountData): Discount
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

    private static function unserializePeriod(array $periodData): PromotionPeriod
    {
        return new PromotionPeriod(
            \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $periodData['from']),
            \DateTimeImmutable::createFromFormat(\DateTime::RFC3339, $periodData['to'])
        );
    }

    private static function unserializeRule(array $ruleData): CatalogRule
    {
        $type = new CatalogRuleType($ruleData['type']);

        switch (true) {
            case CatalogRuleType::OR()->equals($type):
                return new OrCatalogRule(...array_map([self::class, 'unserializeRule'], $ruleData['items']));
            case CatalogRuleType::AND()->equals($type):
                return new AndCatalogRule(...array_map([self::class, 'unserializeRule'], $ruleData['items']));
            case CatalogRuleType::PRODUCT_IN_CATEGORY_LIST()->equals($type):
                return new ProductInCategoryListRule(...$ruleData['categories_ids']);
            case CatalogRuleType::PRODUCT_IN_LIST()->equals($type):
                return new ProductInListRule(...$ruleData['products_ids']);
            case CatalogRuleType::PRODUCT_PRICE_INFERIOR_TO()->equals($type):
                return new ProductPriceInferiorToRule(to_float($ruleData['value']));
            case CatalogRuleType::PRODUCT_PRICE_SUPERIOR_TO()->equals($type):
                return new ProductPriceSuperiorToRule(to_float($ruleData['value']));
            default:
                throw new \Exception('unexpected rule type');
        }
    }
}
