<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

use function theodorejb\polycast\to_int;

final class OrderItem
{
    /** @var int */
    private $itemId;

    /** @var int */
    private $productId;

    /** @var int */
    private $quantity;

    /** @var float */
    private $discountAmount;

    /** @var float */
    private $price;

    /** @var string */
    private $code;

    /** @var int */
    private $quantityShipped;

    /** @var int */
    private $quantityToShip;

    /** @var array */
    private $optionsVariantsIds;

    /** @var string */
    private $comment;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->productId = $data['product_id'];
        $this->itemId = to_int($data['item_id']);
        $this->quantity = $data['amount'];
        $this->discountAmount = $data['discount'];
        $this->price = $data['price'];
        $this->code = $data['selected_code'];
        $this->quantityShipped = $data['shipped_amount'];
        $this->quantityToShip = to_int($data['shipment_amount']);
        $this->optionsVariantsIds = [];
        foreach ($data['extra']['combinations'] ?? [] as $combinationData) {
            $this->optionsVariantsIds[$combinationData['option_name']] = $combinationData['variant_name'];
        }
        $this->comment = $data['comment'] ?? '';
    }

    public function getItemId(): int
    {
        return $this->itemId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getDiscountAmount(): float
    {
        return $this->discountAmount;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getQuantityShipped(): int
    {
        return $this->quantityShipped;
    }

    public function getQuantityToShip(): int
    {
        return $this->quantityToShip;
    }

    public function getOptionsVariantsIds(): array
    {
        return $this->optionsVariantsIds;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}
