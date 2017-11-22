<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

class ProductAttachment
{
    /** @var string */
    private $label;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->label = $data['label'];
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
