<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Search;

final class Attribute
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $slug;

    /** @var array */
    private $values;

    /**
     * @internal
     */
    public function __construct($data)
    {
        $this->id = $data['attribute']['id'];
        $this->name = $data['attribute']['name'];
        $this->slug = $data['attribute']['slug'] ?? '';
        $this->values = $data['values'] ?? [];
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

    public function getValues(): array
    {
        return $this->values;
    }
}
