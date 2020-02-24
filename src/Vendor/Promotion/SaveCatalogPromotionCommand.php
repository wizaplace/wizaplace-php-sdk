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
use Wizaplace\SDK\Vendor\Promotion\Rules\CatalogRule;

/**
 * Class SaveCatalogPromotionCommand
 * @package Wizaplace\SDK\Vendor\Promotion
 */
final class SaveCatalogPromotionCommand
{
    /** @var null|string */
    private $promotionId;

    /** @var null|string */
    private $name;

    /** @var null|bool */
    private $active;

    /** @var null|CatalogRule */
    private $rule;

    /** @var null|(Discount[]) */
    private $discounts;

    /** @var null|PromotionPeriod */
    private $period;

    /**
     * SaveCatalogPromotionCommand constructor.
     *
     * @param string|null $promotionId
     */
    private function __construct(?string $promotionId = null)
    {
        $this->promotionId = $promotionId;
    }

    /**
     * @return SaveCatalogPromotionCommand
     */
    public static function createNew(): self
    {
        return new self(null);
    }

    /**
     * @param string $promotionId
     *
     * @return SaveCatalogPromotionCommand
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
     * @return SaveCatalogPromotionCommand
     */
    public function setPromotionId(?string $promotionId): self
    {
        $this->promotionId = $promotionId;

        return $this;
    }

    /**
     * @param string|null $name
     *
     * @return SaveCatalogPromotionCommand
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param bool|null $active
     *
     * @return SaveCatalogPromotionCommand
     */
    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @param CatalogRule|null $rule
     *
     * @return SaveCatalogPromotionCommand
     */
    public function setRule(?CatalogRule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * @param array|null $discounts The complete list of discounts provided by the promotion. If null, won't modify existing discounts. If empty, will remove them all.
     *
     * @return SaveCatalogPromotionCommand
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
     * @return SaveCatalogPromotionCommand
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
        $serializer = new Serializer(
            [
                new DateTimeNormalizer(),
                new CustomNormalizer(),
                new GetSetMethodNormalizer(null, new CamelCaseToSnakeCaseNameConverter()),
            ]
        );

        return array_filter(
            $serializer->normalize(
                [
                    'promotion_id' => $this->promotionId,
                    'name' => $this->name,
                    'active' => $this->active,
                    'rule' => $this->rule,
                    'discounts' => $this->discounts,
                    'period' => $this->period,
                ]
            ),
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
