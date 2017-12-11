<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

final class CreateProductCommand extends ProductUpsertData
{
    protected static function allowsPartialData(): bool
    {
        return false;
    }
}
