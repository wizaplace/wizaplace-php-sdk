<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Category;

use Wizaplace\SDK\AbstractService;

/**
 * Class CategoryService
 * @package Wizaplace\SDK\Pim\Category
 */
class CategoryService extends AbstractService
{
    /**
     * @return Category[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function listCategories(): array
    {
        $this->client->mustBeAuthenticated();
        $categoriesData = $this->client->get('categories', ['leaves' => true]);

        return array_map(
            static function ($categoryData) {
                return new Category($categoryData);
            },
            $categoriesData
        );
    }

    /**
     * @return Category|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getCategory(int $categoryId): ?Category
    {
        $this->client->mustBeAuthenticated();

        $categoryData = $this->client->get('categories/' . $categoryId);
        if ($categoryData !== null) {
            return new Category($categoryData);
        }

        return null;
    }
}
