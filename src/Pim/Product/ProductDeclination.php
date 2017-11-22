<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

final class ProductDeclination
{
    /** @var int */
    private $amount;

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->amount = $data['amount'];
        if (isset($data['combination_code'])) {
            $this->code = $data['combination_code'];
        }
        $this->optionsVariants = [];
        // We don't copy the full array directly so we can cast the keys (which are strings because that's how JSON works)
        foreach ($data['combination'] as $optionId => $optionVariantId) {
            $this->optionsVariants[(int) $optionId] = $optionVariantId;
        }
        $this->price = (float) $data['price'];
        if (isset($data['crossed_out_price'])) {
            $this->crossedOutPrice = (float) $data['crossed_out_price'];
        }
        if (isset($data['affiliate_link']) && $data['affiliate_link'] !== '') {
            $this->affiliateLink = new Uri($data['affiliate_link']);
        }
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return int[] a map from (int) option ID to (int) variant ID
     */
    public function getOptionsVariants(): array
    {
        return $this->optionsVariants;
    }

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
}
