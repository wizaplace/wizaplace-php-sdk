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
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketRule;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketPromotionTarget;

/**
 * Class SaveBasketPromotionCommand
 * @package Wizaplace\SDK\Vendor\Promotion
 */
final class SaveBasketPromotionCommand
{
    /** @var null|string */
    private $promotionId;

    /** @var null|string */
    private $name;

    /** @var null|bool */
    private $active;

    /** @var null|BasketRule */
    private $rule;

    /** @var null|(Discount[]) */
    private $discounts;

    /** @var null|PromotionPeriod */
    private $period;

    /** @var null|string */
    private $coupon;

    /** @var null|BasketPromotionTarget */
    private $target;

    /**
     * SaveBasketPromotionCommand constructor.
     *
     * @param string|null $promotionId
     */
    private function __construct(?string $promotionId = null)
    {
        $this->promotionId = $promotionId;
    }

    /**
     * @return SaveBasketPromotionCommand
     */
    public static function createNew(): self
    {
        return new self(null);
    }

    /**
     * @param string $promotionId
     *
     * @return SaveBasketPromotionCommand
     */
    public static function updateExisting(string $promotionId): self
    {
        return new self($promotionId);
    }

    /**
     * @return string|null
     */
    public function getPromotionId(): ?string
    {
        return $this->promotionId;
    }

    /**
     * @param string|null $promotionId
     *
     * @return SaveBasketPromotionCommand
     */
    public function setPromotionId(?string $promotionId): self
    {
        $this->promotionId = $promotionId;

        return $this;
    }

    /**
     * @param string|null $name
     *
     * @return SaveBasketPromotionCommand
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param bool|null $active
     *
     * @return SaveBasketPromotionCommand
     */
    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param BasketRule|null $rule
     *
     * @return SaveBasketPromotionCommand
     */
    public function setRule(?BasketRule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCoupon(): ?string
    {
        return $this->coupon;
    }

    /**
     * @param string|null $coupon
     *
     * @return SaveBasketPromotionCommand
     */
    public function setCoupon(?string $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    /**
     * @return BasketPromotionTarget|null
     */
    public function getTarget(): ?BasketPromotionTarget
    {
        return $this->target;
    }

    /**
     * @param BasketPromotionTarget|null $target
     *
     * @return SaveBasketPromotionCommand
     */
    public function setTarget(?BasketPromotionTarget $target): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @param array|null $discounts
     *
     * @return SaveBasketPromotionCommand
     */
    public function setDiscounts(?array $discounts): self
    {
        if ($discounts !== null) {
            $this->setNonNullDiscounts(...$discounts);
        } else {
            $this->discounts = null;
        }


        return $this;
    }

    /**
     * @param PromotionPeriod|null $period
     *
     * @return SaveBasketPromotionCommand
     */
    public function setPeriod(?PromotionPeriod $period): self
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @internal
     *
     * @return array
     */
    public function toArray(): array
    {
        $serializer = new Serializer([
            new DateTimeNormalizer(),
            new CustomNormalizer(),
            new GetSetMethodNormalizer(null, new CamelCaseToSnakeCaseNameConverter()),
        ]);

        return array_filter(
            $serializer->normalize([
                'promotion_id' => $this->promotionId,
                'name' => $this->name,
                'active' => $this->active,
                'rule' => $this->rule,
                'discounts' => $this->discounts,
                'period' => $this->period,
                'coupon' => $this->coupon,
                'target' => $this->target,
            ]),
            static function ($value): bool {
                return $value !== null;
            }
        );
    }

    /**
     * @param Discount ...$discounts
     */
    private function setNonNullDiscounts(Discount ...$discounts): void
    {
        // this function is here just for the type-check
        $this->discounts = $discounts;
    }
}
