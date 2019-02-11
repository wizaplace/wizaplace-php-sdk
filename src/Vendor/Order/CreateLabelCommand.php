<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Vendor\Order;

use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

/**
 * Class CreateLabelCommand
 * @package Wizaplace\SDK\Vendor\Order
 */
final class CreateLabelCommand
{
    /** @var int[] map of (int) itemId to (int) quantity shipped */
    private $shippedQuantityByItemId;

    /**
     * @param int[] $shippedQuantityByItemId map of (int) itemId to (int) quantity shipped
     *
     * @return self
     */
    public function setShippedQuantityByItemId(array $shippedQuantityByItemId): self
    {
        $this->shippedQuantityByItemId = $shippedQuantityByItemId;

        return $this;
    }

    /**
     * @internal
     * @throws SomeParametersAreInvalid
     */
    public function validate(): void
    {
        if (count($this->shippedQuantityByItemId) === 0) {
            throw new SomeParametersAreInvalid('at least 1 order item id is required');
        }
    }

    /**
     * @internal
     */
    public function toArray()
    {
        return [
            'products' => $this->shippedQuantityByItemId,
        ];
    }
}
