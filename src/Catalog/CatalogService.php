<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\ApiClientInjection;
use Wizaplace\Exception\NotFound;

class CatalogService
{
    use ApiClientInjection;

    public function getProductById(int $id) : Product
    {
        try {
            $response = $this->client->get("catalog/products/{$id}");
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Product #{$id} not found.", $exception);
            } else {
                throw $exception;
            }
        }

        return new Product($response);
    }

    /**
     * @return CategoryTree[]
     */
    public function getCategoryTree():array
    {
        $categoryTree = $this->client->get('catalog/categories/tree');

        return array_map(
            function ($tree) {
                return new CategoryTree($tree);
            },
            $categoryTree
        );
    }

    public function getCategory(int $id): Category
    {
        $category = $this->client->get("catalog/categories/{$id}");

        return new Category($category);
    }

    public function search(string $query = '', array $filters = [], array $sorting = [], int $resultsPerPage = 12, int $page = 1): SearchResult
    {
        $results = $this->client->get(
            'catalog/search/products',
            [
                'query' => [
                    'filters' => $filters,
                    'sorting' => $sorting,
                    'resultsPerPage' => $resultsPerPage,
                    'page' => $page,
                    'query' => $query,
                ],
            ]
        );

        return new SearchResult($results);
    }
}
