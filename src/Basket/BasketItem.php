<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Basket;

use Wizaplace\Image\Image;

final class BasketItem
{
    /** @var string */
    private $declinationId;

    /** @var int */
    private $productId;

    /** @var string */
    private $productName;

    /** @var string */
    private $productUrl;

    /** @var float */
    private $individualPrice;

    /** @var float */
    private $crossedOutPrice;

    /** @var Image */
    private $mainImage;

    /** @var int */
    private $quantity;

    /** @var float */
    private $total;

    /** @var DeclinationOption[] */
    private $declinationOptions;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->declinationId = $data['declinationId'];
        $this->productId = $data['productId'];
        $this->productName = $data['productName'];
        $this->productUrl = $data['productUrl'];
        $this->individualPrice = $data['individualPrice'];
        $this->crossedOutPrice = $data['crossedOutPrice'];
        if ($data['mainImage']) {
            $this->mainImage = new Image($data['mainImage']);
        }
        $this->quantity = $data['quantity'];
        $this->total = $data['total'];
        $this->declinationOptions = array_map(static function (array $data) : DeclinationOption {
            return new DeclinationOption($data);
        }, $data['options'] ?? []);
    }

    public function getDeclinationId(): string
    {
        return $this->declinationId;
    }

    /**
     * @return DeclinationOption[]
     */
    public function getDeclinationOptions(): array
    {
        return $this->declinationOptions;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductUrl(): string
    {
        return $this->productUrl;
    }

    public function getIndividualPrice(): float
    {
        return $this->individualPrice;
    }

    public function getCrossedOutPrice(): ?float
    {
        return $this->crossedOutPrice;
    }

    public function getMainImage(): ?Image
    {
        return $this->mainImage;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getTotal(): float
    {
        return $this->total;
    }
}
