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

        $category = end($categories);

        $this->assertSame(1, $category->getId());
        $this->assertSame("Echec import CSV", $category->getName());
        $this->assertSame(0, $category->getParentId());
        $this->assertTrue(CategoryStatus::HIDDEN()->equals($category->getStatus()));
    }

    public function testGetCategory(): void
    {
        $category = $this->buildCategoryService()->getCategory(3);

        static::assertNotEmpty($category);
        static::assertSame(3, $category->getId());
        static::assertSame(18, $category->getAgeLimit());
    }

    private function buildCategoryService($userEmail = 'vendor@wizaplace.com', $userPassword = 'password'): CategoryService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new CategoryService($apiClient);
    }
}
