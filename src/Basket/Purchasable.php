<?php
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

interface Purchasable
{
    /**
     * @internal
     * @return string an ID which can be added to a basket (declinationId, or productId in some cases)
     */
    public function getPurchasableId(): string;
}
