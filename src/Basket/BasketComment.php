<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Basket;

final class BasketComment extends Comment
{
    public function toArray(): array
    {
        return [
            'comment' => $this->comment,
        ];
    }
}
