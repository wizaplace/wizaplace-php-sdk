<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Wizaplace\SDK\AbstractService;

final class ProductService extends AbstractService
{
    public function getProductById(int $productId): Product
    {
        $this->client->mustBeAuthenticated();
        $data = $this->client->get("products/$productId");

        return new Product($data);
    }
}
