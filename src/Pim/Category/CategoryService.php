<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Pim\Category;

use Wizaplace\SDK\AbstractService;

class CategoryService extends AbstractService
{
    public function listCategories(): array
    {
        $categoriesData = $this->client->get('categories', ['leaves' => true]);

        return array_map(static function ($categoryData) {
            return new Category($categoryData);
        }, $categoriesData);
    }
}
