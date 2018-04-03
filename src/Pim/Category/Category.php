<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Pim\Category;

class Category
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var CategoryStatus */
    private $status;

    /** @var int */
    private $parentId;

    public function __construct(array $data)
    {
        $this->id = $data['category_id'];
        $this->name = $data['category'];
        $this->status = new CategoryStatus($data['status']);
        $this->parentId = $data['parent_id'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): CategoryStatus
    {
        return $this->status;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }
}
