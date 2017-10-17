<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Basket;

final class BasketComment extends Comment
{
    /**
     * @internal
     */
    public function toArray(): array
    {
        return [
            'comment' => $this->comment,
        ];
    }
}
