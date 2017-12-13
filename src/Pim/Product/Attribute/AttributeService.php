<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\Attribute;

use Wizaplace\SDK\AbstractService;

final class AttributeService extends AbstractService
{
    /**
     * @return ProductAttribute[]
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function getProductAttributes(int $productId): array
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("products/${productId}/features");

        return array_map(static function (array $attributeData): ProductAttribute {
            return ProductAttribute::build($attributeData);
        }, $data);
    }

    /**
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function getProductAttribute(int $productId, int $attributeId): ProductAttribute
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("products/${productId}/features/${attributeId}");

        return ProductAttribute::build($data);
    }

    /**
     * @return CategoryAttribute[]
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function getCategoryAttributes(int $categoryId): array
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("categories/${categoryId}/features");

        return array_map(static function (array $attributeData): CategoryAttribute {
            return new CategoryAttribute($attributeData);
        }, $data);
    }
}
