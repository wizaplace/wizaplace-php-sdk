<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

final class SearchCategoryPath implements \JsonSerializable
{
    /** @var int */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $slug;

    /**
     * @internal
     */
    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->slug = $data['slug'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
        ];
    }
}
