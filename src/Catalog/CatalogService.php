<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog;

use GuzzleHttp\Client;
use Wizaplace\AbstractService;

class CatalogService extends AbstractService
{
    public function getProductById(int $id) : Product
    {
        $response = $this->client->request('GET', "catalog/products/{$id}");

        return new Product(json_decode($response->getBody()->getContents(), true));
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

    public function search($query = '', $filters = [], $sorting = [], $resultsPerPage = 12, $page = 1): SearchResult
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
