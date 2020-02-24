<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;

/**
 * Class AttributeService
 * @package Wizaplace\SDK\Pim\Attribute
 */
final class AttributeService extends AbstractService
{
    /**
     * @param int $productId
     *
     * @return ProductAttribute[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getProductAttributes(int $productId): array
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("products/${productId}/features");

        return array_map(
            static function (array $attributeData): ProductAttribute {
                return ProductAttribute::build($attributeData);
            },
            $data
        );
    }

    /**
     * @param int $productId
     * @param int $attributeId
     *
     * @return ProductAttribute
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getProductAttribute(int $productId, int $attributeId): ProductAttribute
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("products/${productId}/features/${attributeId}");

        return ProductAttribute::build($data);
    }

    /**
     * @param int $categoryId
     *
     * @return Attribute[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getCategoryAttributes(int $categoryId): array
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("categories/${categoryId}/features");

        return array_map(
            static function (array $attributeData): Attribute {
                return new Attribute($attributeData);
            },
            $data
        );
    }

    /**
     * @param int    $productId
     * @param int    $attributeId
     * @param string $value
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function setProductAttributeValue(int $productId, int $attributeId, string $value): void
    {
        $this->client->mustBeAuthenticated();

        $this->client->put(
            "products/${productId}/features/${attributeId}",
            [
                RequestOptions::JSON => [
                    'value_str' => $value,
                ],
            ]
        );
    }

    /**
     * @param int   $productId
     * @param int   $attributeId
     * @param array $variantIds
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function setProductAttributeVariants(int $productId, int $attributeId, array $variantIds): void
    {
        $this->client->mustBeAuthenticated();

        $this->client->put(
            "products/${productId}/features/${attributeId}",
            [
                RequestOptions::JSON => [
                    'variant_id' => $variantIds,
                ],
            ]
        );
    }
}
