<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\MultiVendorProduct;

final class MultiVendorProductVideo
{
    /**
     * @var string|null
     */
    private $id;
    /**
     * @var string|null
     */
    private $thumb;
    /**
     * @var string|null
     */
    private $path;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->thumb = $data['thumb'] ?? null;
        $this->path = $data['path'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getThumb(): ?string
    {
        return $this->thumb;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }
}
