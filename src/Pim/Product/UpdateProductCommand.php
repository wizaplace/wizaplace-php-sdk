<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

/**
 * Holds the data for a product update.
 * Partial updates are allowed (you do not need to set all fields).
 */
final class UpdateProductCommand extends ProductUpsertData
{
    /**
     * @var int
     */
    private $productId;

    public function __construct(int $productId)
    {
        $this->productId = $productId;
    }

    public function getId(): int
    {
        return $this->productId;
    }
}
