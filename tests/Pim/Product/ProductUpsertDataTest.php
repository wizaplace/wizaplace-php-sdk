<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Pim\Product;

use PHPUnit\Framework\TestCase;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Pim\Product\CreateProductCommand;
use Wizaplace\SDK\Pim\Product\ProductImageUpload;
use Wizaplace\SDK\Pim\Product\ProductStatus;
use Wizaplace\SDK\Pim\Product\ProductUpsertData;

final class ProductUpsertDataTest extends TestCase
{
    public function testValidateBlankFails(): void
    {
        $data = new CreateProductCommand();
        $this->expectException(SomeParametersAreInvalid::class);
        $data->validate();
    }

    public function testMinimumValidData(): void
    {
        $data = $this->minimumValidData();
        $data->validate();
        $array = $data->toArray();
        $this->assertEquals(array (
            'product_code' => '',
            'supplier_ref' => '',
            'product' => '',
            'status' => 'A',
            'main_category' => 1,
            'green_tax' => 0.1,
            'condition' => 'U',
            'weight' => 0.1,
            'full_description' => '',
            'short_description' => '',
            'tax_ids' =>
                array (
                ),
            'inventory' =>
                array (
                ),
            'allowed_options_variants' =>
                array (
                ),
        ), $array);
    }

    public function testIncompleteProductImageFails(): void
    {
        $this->expectException(SomeParametersAreInvalid::class);
        $this->minimumValidData()
            ->setAdditionalImages([new ProductImageUpload()])
            ->validate();
    }

    private function minimumValidData(): ProductUpsertData
    {
        return (new CreateProductCommand())
            ->setCode("")
            ->setGreenTax(0.1)
            ->setIsBrandNew(false)
            ->setName("")
            ->setSupplierReference('')
            ->setStatus(ProductStatus::ENABLED())
            ->setMainCategoryId(1)
            ->setWeight(0.1)
            ->setFullDescription("")
            ->setShortDescription("")
            ->setTaxIds([])
            ->setDeclinations([]);
    }
}
