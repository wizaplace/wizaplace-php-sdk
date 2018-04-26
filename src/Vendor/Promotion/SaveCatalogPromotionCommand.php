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

    private function __construct(?string $promotionId = null)
    {
        $this->promotionId = $promotionId;
    }

    public static function createNew(): self
    {
        return new self(null);
    }

    public static function updateExisting(string $promotionId): self
    {
        return new self($promotionId);
    }

    public static function createDuplicate(CatalogPromotion $src): self
    {
        return self::createNew()
            ->setName($src->getName())
            ->setActive($src->isActive());
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

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function setRule(?CatalogRule $rule): self
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * @param null|(Discount[]) $discounts The complete list of discounts provided by the promotion. If null, won't modify existing discounts. If empty, will remove them all.
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

    public function setPeriod(?PromotionPeriod $period): self
    {
        $this->period = $period;

        return $this;
    }

    /**
     * @internal
     */
    public function toArray(): array
    {
        $serializer = new Serializer([
            new DateTimeNormalizer(\DateTime::RFC3339),
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
            ]),
            static function ($value): bool {
                return $value !== null;
            }
        );
    }

    private function setNonNullDiscounts(Discount ...$discounts): void
    {
        // this function is here just for the type-check
        $this->discounts = $discounts;
    }
}
