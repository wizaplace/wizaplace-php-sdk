<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\RelatedProduct;

class RelatedProduct
{
    /** @var string */
    private $type;

    /** @var int */
    private $productId;

    /** @var null|string */
    private $description;

    /** @var null|string */
    private $extra;

    public function __construct(array $data)
    {
        $this->productId = $data['productId'];
        $this->type = $data['type'];
        $this->description = $data['description'];
        $this->extra = $data['extra'];
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getExtra(): ?string
    {
        return $this->extra;
    }

    public function jsonSerialize(): array
    {
        return [
            'productId' => $this->getProductId(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
            'extra' => $this->getExtra(),
        ];
    }
}
