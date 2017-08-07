<?php
declare(strict_types = 1);

namespace Wizaplace\Favorite;

use Wizaplace\Image\Image;

final class Favorite
{
    /** @var int */
    private $productId;

    /** @var string */
    private $name;

    /** @var string */
    private $code;

    /** @var string */
    private $supplierReference;

    /** @var float */
    private $price;

    /** @var float */
    private $priceWithoutVat;

    /** @var float */
    private $greenTax;

    /** @var float */
    private $vat;

    /** @var int */
    private $quantity;

    /** @var string */
    private $affiliateLink;

    /** @var Image[] */
    private $images;

    /** @var string */
    private $slug;

    /** @var string */
    private $categorySlugPath;

    public function __construct(array $data)
    {
        $this->productId = $data['productId'];
        $this->name = $data['name'];
        $this->code = $data['code'];
        $this->supplierReference = $data['supplierReference'];
        $this->price = $data['price'];
        $this->priceWithoutVat = $data['prices']['priceWithoutVat'];
        $this->greenTax = $data['greenTax'];
        $this->vat = $data['prices']['vat'];
        $this->quantity = $data['quantity'];
        $this->affiliateLink = $data['affiliateLink'];
        $this->images = array_map(
            static function (array $data): Image {
                return new Image($data);
            },
            $data['images']
        );
        $this->slug = $data['slug'];
        $this->categorySlugPath = $data['categorySlugPath'];
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSupplierReference(): string
    {
        return $this->supplierReference;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getPriceWithoutVat(): float
    {
        return $this->priceWithoutVat;
    }

    public function getGreenTax(): float
    {
        return $this->greenTax;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getAffiliateLink(): string
    {
        return $this->affiliateLink;
    }

    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCategorySlugPath(): string
    {
        return $this->categorySlugPath;
    }
}
