<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Catalog\Search;

use GuzzleHttp\Psr7\Response;
use Wizaplace\SDK\Catalog\Search\SearchService;
use Wizaplace\SDK\Tests\ApiTestCase;

/**
 * @see SearchService
 */
final class SearchServiceTest extends ApiTestCase
{
    public function testSearchOneProductByName()
    {
        $searchService = $this->buildSearchService();

        $result = $searchService->search('Product');

        $products = $result->getProducts();
        $this->assertCount(3, $products);

        $product = $products[0];
        $this->assertSame('4', $product->getId());
        $this->assertSame('product-with-shippings', $product->getSlug());
        $this->assertTrue($product->isAvailable());
        $this->assertGreaterThan(1400000000, $product->getCreatedAt()->getTimestamp());
        $this->assertNull($product->getAverageRating());
        $this->assertSame('Product with shippings', $product->getName());
        $this->assertSame(['N'], $product->getCondition());
        $this->assertSame(1, $product->getDeclinationCount());
        $this->assertSame('', $product->getSubtitle());
        $this->assertSame("La nouvelle génération de notre tablette Fire phare - désormais plus fine, plus légère, dotée d'une plus longue autonomie et d'un écran amélioré.", $product->getShortDescription());
        $this->assertGreaterThanOrEqual($product->getCreatedAt()->getTimestamp(), $product->getUpdatedAt()->getTimestamp());
        $this->assertNull($product->getAffiliateLink());
        $this->assertSame(['special-category-dedicated-to-specific-tests'], $product->getCategorySlugs());
        $this->assertCount(1, $product->getCategoryPath());
        $this->assertNull($product->getMainImage());
        $this->assertSame(9.9, $product->getMinimumPrice());
        $this->assertNull($product->getCrossedOutPrice());
        $this->assertCount(0, $product->getAttributes());
        $companies = $product->getCompanies();
        $this->assertCount(1, $companies);
        $this->assertSame('The World Company Inc.', $companies[0]->getName());
        $this->assertSame('the-world-company-inc.', $companies[0]->getSlug());
        $this->assertSame(3, $companies[0]->getId());
        $this->assertTrue($companies[0]->isProfessional());
        $this->assertNull($companies[0]->getImage());
        $this->assertNull($companies[0]->getAverageRating());


        $pagination = $result->getPagination();
        $this->assertSame(1, $pagination->getNbPages());
        $this->assertSame(3, $pagination->getNbResults());
        $this->assertSame(1, $pagination->getPage());
        $this->assertSame(12, $pagination->getResultsPerPage());

        $facets = $result->getFacets();
        $this->assertCount(9, $facets);
        $this->assertSame('categories', $facets[0]->getName());
        $this->assertSame('Catégorie', $facets[0]->getLabel());
        $this->assertSame([
            5 => [
                'label' => 'Special category dedicated to specific tests',
                'count' => '3',
                'position' => '0',
            ],
        ], $facets[0]->getValues());
        $this->assertFalse($facets[0]->isIsNumeric());
    }

    private function buildSearchService(): SearchService
    {
        return new SearchService($this->buildApiClient());
    }
}
