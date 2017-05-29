<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;

class CatalogService extends AbstractService
{
    public function getProductById(int $id) : Product
    {
        try {
            $response = $this->get("catalog/products/{$id}");
        } catch (ClientException $exception) {
            if ($exception->getCode() === 404) {
                throw new NotFound("Product #{$id} not found.", 404, $exception);
            } else {
                throw $exception;
            }
        }

        return new Product($response);
    }

    /**
     * @return CatalogCategoryTree[]
     */
    public function getCategoryTree():array
    {
        $categoryTree = $this->get('catalog/categories/tree');

        return array_map(
            function ($tree) {
                return new CatalogCategoryTree($tree);
            },
            $categoryTree
        );
    }

    public function getCategory(int $id): CatalogCategory
    {
        $category = $this->get("catalog/categories/{$id}");

        return new CatalogCategory($category);
    }

    public function search(string $query = '', array $filters = [], array $sorting = [], int $resultsPerPage = 12, int $page = 1): SearchResult
    {
        $results = $this->get(
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
