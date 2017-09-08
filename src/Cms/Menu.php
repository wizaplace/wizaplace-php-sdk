<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Cms;

final class Menu
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var MenuItem[] */
    private $items;

    /**
     * @param MenuItem[] $items
     * @internal
     */
    public function __construct(int $id, string $name, array $items)
    {
        $this->id = $id;
        $this->name = $name;

        $this->setItems(...$items);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return MenuItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    private function setItems(MenuItem ...$items)
    {
        $this->items = $items;
    }
}
