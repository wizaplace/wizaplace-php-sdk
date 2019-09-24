<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;
use function theodorejb\polycast\to_float;
use function theodorejb\polycast\to_int;

/**
 * Class ProductDeclination
 * @package Wizaplace\SDK\Pim\Product
 */
final class ProductDeclination
{
    /** @var int */
    private $quantity;

    /** @var null|string */
    private $code;

    /** @var float */
    private $price;

    /** @var null|float */
    private $crossedOutPrice;

    /** @var null|UriInterface */
    private $affiliateLink;

    /** @var int[] */
    private $optionsVariants;

    /** @var PriceTier[] */
    private $priceTiers;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->quantity = $data['amount'];
        if (isset($data['combination_code']) && $data['combination_code'] !== '') {
            $this->code = $data['combination_code'];
        }
        $this->optionsVariants = [];
        // We don't copy the full array directly so we can cast the keys (which are strings because that's how JSON works)
        foreach ($data['combination'] as $optionId => $optionVariantId) {
            $this->optionsVariants[to_int($optionId)] = to_int($optionVariantId);
        }
        $this->price = to_float($data['price']);
        if (isset($data['crossed_out_price']) && $data['crossed_out_price'] !== 0.0) {
            $this->crossedOutPrice = to_float($data['crossed_out_price']);
        }
        if (isset($data['affiliate_link']) && $data['affiliate_link'] !== '') {
            $this->affiliateLink = new Uri($data['affiliate_link']);
        }

        $this->priceTiers = [];
        if (\array_key_exists('price_tiers', $data) && \is_array($data['price_tiers'])) {
            foreach ($data['price_tiers'] as $priceTier) {
                $this->addPriceTier($priceTier);
            }
        }
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /** @return int[] a map from (int) option ID to (int) variant ID */
    public function getOptionsVariants(): array
    {
        return $this->optionsVariants;
    }

    /** @return string|null */
    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    public function getAffiliateLink(): ?UriInterface
    {
        return $this->affiliateLink;
    }

    public function addPriceTier(array $priceTier): void
    {
        $this->priceTiers[] = new PriceTier($priceTier);
    }

    /** @return PriceTier[] */
    public function getPriceTiers(): array
    {
        return $this->priceTiers;
    }
}
