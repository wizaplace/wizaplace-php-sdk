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

/**
 * Class ProductDeclinationUpsertData
 * @package Wizaplace\SDK\Pim\Product
 */
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

    /** @var null|bool */
    private $infiniteStock;

    /** @var array */
    private $priceTiers;

    /**
     * ProductDeclinationUpsertData constructor.
     *
     * @param array $optionsVariants
     */
    public function __construct(array $optionsVariants)
    {
        $this->setOptionsVariants($optionsVariants);
    }


    /**
     * @param mixed[] $priceTiers
     *
     * @return ProductDeclinationUpsertData
     */
    public function setPriceTiers(array $priceTiers): self
    {
        $this->priceTiers = $priceTiers;

        return $this;
    }

    /**
     * @param int $quantity
     *
     * @return ProductDeclinationUpsertData
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param bool|null $infiniteStock
     *
     * @return ProductDeclinationUpsertData
     */
    public function setInfiniteStock(?bool $infiniteStock): self
    {
        $this->infiniteStock = $infiniteStock;

        return $this;
    }

    /**
     * @param string|null $code
     *
     * @return ProductDeclinationUpsertData
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param float $price
     *
     * @return ProductDeclinationUpsertData
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price ?? 0.0;
    }

    /**
     * @param float|null $crossedOutPrice
     *
     * @return ProductDeclinationUpsertData
     */
    public function setCrossedOutPrice(?float $crossedOutPrice): self
    {
        $this->crossedOutPrice = $crossedOutPrice;

        return $this;
    }

    /**
     * @param UriInterface|null $affiliateLink
     *
     * @return ProductDeclinationUpsertData
     */
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

    /**
     * @param int $optionId
     * @param int $variantId
     *
     * @return ProductDeclinationUpsertData
     */
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
            'infiniteStock',
            'priceTiers',
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
            'priceTiers' => $this->priceTiers,
        ];

        if ($this->crossedOutPrice !== null) {
            $data['crossed_out_price'] = $this->crossedOutPrice;
        }

        if ($this->code !== null) {
            $data['combination_code'] = $this->code;
        }

        if ($this->infiniteStock !== null) {
            $data['infinite_stock'] = $this->infiniteStock;
        }

        return $data;
    }
}
