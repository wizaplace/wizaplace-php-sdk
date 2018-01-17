<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Psr\Http\Message\UriInterface;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class ProductDeclinationUpsertData
{
    /** @var int */
    private $quantity = 0;

    /** @var null|string */
    private $code;

    /** @var float */
    private $price = 0.0;

    /** @var null|float */
    private $crossedOutPrice;

    /** @var null|UriInterface */
    private $affiliateLink;

    /** @var int[] */
    private $optionsVariants;

    public function __construct(array $optionsVariants)
    {
        $this->setOptionsVariants($optionsVariants);
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price ?? 0.0;
    }

    public function setCrossedOutPrice(?float $crossedOutPrice): self
    {
        $this->crossedOutPrice = $crossedOutPrice;

        return $this;
    }

    public function setAffiliateLink(?UriInterface $affiliateLink): self
    {
        $this->affiliateLink = $affiliateLink;

        return $this;
    }

    /**
     * @param array $optionsVariants A map of optionID => variantID. Beware : options that are not available in the product's category will be ignored
     * @return self
     */
    public function setOptionsVariants(array $optionsVariants): self
    {
        $this->optionsVariants = $optionsVariants;

        return $this;
    }

    public function setOptionVariant(int $optionId, int $variantId): self
    {
        $this->optionsVariants[$optionId] = $variantId;

        return $this;
    }

    /**
     * @internal
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        // @TODO: find something more maintainable than this array of strings...
        $nullableProperties = [
            'code',
            'crossedOutPrice',
            'affiliateLink',
        ];
        foreach ($metadata->getReflectionClass()->getProperties() as $prop) {
            if (!in_array($prop->getName(), $nullableProperties)) {
                $metadata->addPropertyConstraint($prop->getName(), new Constraints\NotNull());
            }
        }
    }

    /**
     * @internal
     */
    public function toArray(): array
    {
        $data = [
            'amount' => $this->quantity,
            'price' => $this->price,
            'combination' => $this->optionsVariants,
        ];

        if ($this->crossedOutPrice !== null) {
            $data['crossed_out_price'] = $this->crossedOutPrice;
        }

        if ($this->code !== null) {
            $data['combination_code'] = $this->code;
        }

        return $data;
    }
}
