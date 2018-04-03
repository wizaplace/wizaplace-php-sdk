<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Tests\Pim\Category;

use Wizaplace\SDK\Pim\Category\Category;
use Wizaplace\SDK\Pim\Category\CategoryService;
use Wizaplace\SDK\Pim\Category\CategoryStatus;
use Wizaplace\SDK\Tests\ApiTestCase;

class CategoryServiceTest extends ApiTestCase
{
    public function testListCategories()
    {
        $categories = $this->buildCategoryService()->listCategories();

        $this->assertNotEmpty($categories);

        /** @var Category $category */
        $category = end($categories);

        $this->assertSame(1, $category->getId());
        $this->assertSame("Echec import CSV", $category->getName());
        $this->assertSame(0, $category->getParentId());
        $this->assertTrue(CategoryStatus::HIDDEN()->equals($category->getStatus()));
    }

    private function buildCategoryService($userEmail = 'vendor@wizaplace.com', $userPassword = 'password'): CategoryService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new CategoryService($apiClient);
    }
}
