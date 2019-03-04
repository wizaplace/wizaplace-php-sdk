<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\MultiVendorProduct;

use Wizaplace\SDK\Pagination;

class MultiVendorProductList
{
    /** @var MultiVendorProduct[] */
    private $multiVendorProducts;

    /** @var Pagination */
    private $pagination;

    public function __construct(array $data)
    {
        $this->multiVendorProducts = array_map(function (array $item): MultiVendorProduct {
            return new MultiVendorProduct($item);
        }, $data['_embedded']['multiVendorProducts']);

        $this->pagination = new Pagination([
            'page' => $data['page'],
            'nbResults' => $data['total'],
            'nbPages' => ceil($data['total'] / $data['count']),
            'resultsPerPage' => $data['count'],
        ]);
    }

    /**
     * @return MultiVendorProduct[]
     */
    public function getMultiVendorProducts(): array
    {
        return $this->multiVendorProducts;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }
}
