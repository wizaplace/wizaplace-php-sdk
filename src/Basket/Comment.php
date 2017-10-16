<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Basket;

interface Comment
{
    /**
     * Serialize Comment objects.
     */
    public function toArray(): array;
}
