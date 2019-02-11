<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Pim\Category;

/**
 * Class Category
 * @package Wizaplace\SDK\Pim\Category
 */
final class Category
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var CategoryStatus */
    private $status;

    /** @var int */
    private $parentId;

    /** @internal
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['category_id'];
        $this->name = $data['category'];
        $this->status = new CategoryStatus($data['status']);
        $this->parentId = $data['parent_id'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return CategoryStatus
     */
    public function getStatus(): CategoryStatus
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }
}
