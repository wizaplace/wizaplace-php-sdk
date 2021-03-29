<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Option;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;

/**
 * Class OptionService
 * @package Wizaplace\SDK\Pim\Option
 */
class OptionService extends AbstractService
{
    /**
     * @param int $categoryId
     *
     * @return Option[]
     * @throws NotFound
     */
    public function getCategoryOptions(int $categoryId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $data = $this->client->get("categories/${categoryId}/options");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Category #${categoryId} not found", $e);
            }
            throw $e;
        }

        return array_map(
            function (array $optionData): Option {
                return new Option($optionData);
            },
            $data
        );
    }

    /**
     * @param int $productId
     *
     * @return Option[]
     * @throws NotFound
     */
    public function getProductOptions(int $productId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $data = $this->client->get("products/${$productId}/options");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Product #${$productId} not found", $e);
            }
            throw $e;
        }

        return array_map(
            function (array $optionData): Option {
                return new Option($optionData);
            },
            $data
        );
    }

    /**
     * @param int $optionId
     *
     * @return Option
     * @throws NotFound
     */
    public function getOption(int $optionId): Option
    {
        $this->client->mustBeAuthenticated();

        try {
            $data = $this->client->get("options/${optionId}");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Option #${optionId} not found", $e);
            }
            throw $e;
        }

        return new Option($data);
    }
}
