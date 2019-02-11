<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product;

use Psr\Http\Message\UriInterface;
use function theodorejb\polycast\to_string;

/**
 * Class ProductAttachmentUpload
 * @package Wizaplace\SDK\Pim\Product
 */
class ProductAttachmentUpload
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $url;

    /**
     * ProductAttachmentUpload constructor.
     * @param string $label
     * @param string|UriInterface $url
     */
    public function __construct(string $label, $url)
    {
        $this->label = $label;

        $this->url = to_string($url);
    }

    /**
     * @internal
     * @return array
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'url' => $this->url,
        ];
    }
}
