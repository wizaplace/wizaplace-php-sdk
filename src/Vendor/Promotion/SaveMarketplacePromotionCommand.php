<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

use Symfony\Component\Serializer\Normalizer\CustomNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Wizaplace\SDK\Vendor\Promotion\Discounts\Discount;
use Wizaplace\SDK\Vendor\Promotion\Rules\BasketRule;
use Wizaplace\SDK\Vendor\Promotion\Targets\BasketPromotionTarget;

final class SaveMarketplacePromotionCommand
{
    /** @var null|string */
    private $promotionId;

    /** @var null|string */
    private $name;

    /** @var null|bool */
    private $active;

    /** @var null|BasketRule */
    private $rule;

    /** @var null|Discount[] */
    private $discounts;

    /** @var null|PromotionPeriod */
    private $period;

    /** @var string */
    private $coupon;

    /** @var null|BasketPromotionTarget */
    private $target;

    private function __construct(?string $promotionId = null)
    {
        $this->promotionId = $promotionId;
    }

    public static function createNew(): self
    {
        return new self();
    }

    public static function updateExisting(string $promotionId): self
    {
        return new self($promotionId);
    }

    public function getPromotionId(): ?string
    {
        return $this->promotionId;
    }

    public function setPromotionId(?string $promotionId): self
    {
        $this->promotionId = $promotionId;

        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function setRule(?BasketRule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    public function getCoupon(): string
    {
        return $this->coupon;
    }

    public function setCoupon(string $coupon): self
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getTarget(): ?BasketPromotionTarget
    {
        return $this->target;
    }

    public function setTarget(?BasketPromotionTarget $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function setDiscounts(?array $discounts): self
    {
        if ($discounts !== null) {
            $this->setNotNullDiscounts(...$discounts);
        } else {
            $this->discounts = null;
        }

        return $this;
    }

    public function setPeriod(?PromotionPeriod $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function toArray(): array
    {
        $serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new CustomNormalizer(),
                new GetSetMethodNormalizer(null),
            ]
        );

        $array = array_filter(
            $serializer->normalize(
                [
                    'name' => $this->name,
                    'active' => $this->active,
                    'rule' => $this->rule,
                    'discounts' => $this->discounts,
                    'period' => $this->period,
                    'coupon' => $this->coupon,
                    'target' => $this->target,
                ]
            ),
            function ($value): bool {
                return $value !== null;
            }
        );

        //normalise discounts
        if (\array_key_exists('discounts', $array) === true) {
            $serializerDiscount = new Serializer(
                [
                    new DateTimeNormalizer(),
                    new CustomNormalizer(),
                    new GetSetMethodNormalizer(null),
                ]
            );

            $discounts = array_filter(
                $serializerDiscount->normalize(
                    [
                        'discounts' => $this->discounts,
                    ]
                ),
                function ($value): bool {
                    return $value !== null;
                }
            );
            $array['discounts'] = $discounts['discounts'];
        }

        return $array;
    }

    private function setNotNullDiscounts(Discount ...$discounts): void
    {
        $this->discounts = $discounts;
    }
}
