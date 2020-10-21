<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Option;

use Wizaplace\SDK\Pim\Option\Option;
use Wizaplace\SDK\Pim\Option\OptionService;
use Wizaplace\SDK\Pim\Option\OptionVariant;
use Wizaplace\SDK\Tests\ApiTestCase;

class OptionServiceTest extends ApiTestCase
{
    public function testGetCategoryOptions()
    {
        $options = $this->buildOptionService()->getCategoryOptions(4);

        $this->assertContainsOnly(Option::class, $options);
        $this->assertGreaterThanOrEqual(3, \count($options));

        foreach ($options as $option) {
            $this->assertGreaterThan(0, $option->getId());
            $this->assertNotEmpty($option->getName());
            $this->assertNotEmpty($option->getStatus());
            $this->assertTrue($option->isEnabled());
            $variants = $option->getVariants();
            $this->assertContainsOnly(OptionVariant::class, $variants);

            foreach ($variants as $variant) {
                $this->assertGreaterThan(0, $variant->getId());
                $this->assertNotEmpty($variant->getName());
            }
        }
    }

    public function testGetOptionWithDisplayOnFacetingTrue(): void
    {
        $option = $this->buildOptionService()->getOption(1);
        static::assertGreaterThan(0, $option->getId());
        static::assertTrue($option->getDisplayOnFaceting());
    }

    public function testGetOptionWithDisplayOnFacetingFalse(): void
    {
        $option = $this->buildOptionService()->getOption(2);
        static::assertGreaterThan(0, $option->getId());
        static::assertFalse($option->getDisplayOnFaceting());
    }

    private function buildOptionService($userEmail = 'admin@wizaplace.com', $userPassword = 'password'): OptionService
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate($userEmail, $userPassword);

        return new OptionService($apiClient);
    }
}
