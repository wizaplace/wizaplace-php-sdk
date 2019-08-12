<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Option;

use Wizaplace\SDK\AbstractService;

/**
 * Class OptionService
 * @package Wizaplace\SDK\Pim\Option
 */
final class OptionService extends AbstractService
{
    /**
     * @param int $categoryId
     *
     * @return Option[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getCategoryOptions(int $categoryId): array
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("categories/${categoryId}/options");

        return array_map(function (array $optionData): Option {
            return new Option($optionData);
        }, $data);
    }

    /**
     * @param int $productId
     *
     * @return Option[]
     */
    public function getProductOptions(int $productId): array
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("products/${$productId}/options");

        return array_map(function (array $optionData): Option {
            return new Option($optionData);
        }, $data);
    }
}
