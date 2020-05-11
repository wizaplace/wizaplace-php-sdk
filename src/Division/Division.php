<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Division;

use Wizaplace\SDK\User\UserType;

/**
 * A Division entity with its tree properties
 * see getParent and getChildren methods to navigate in the tree
 */
final class Division
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var int
     */
    private $level;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var string
     */
    private $name;

    /**
     * @var null|Division
     */
    private $parent;

    /**
     * @var Division[]
     */
    private $children;

    /**
     * Division constructor.
     *
     * @param array $data
     * @param Division|null $parent
     */
    public function __construct(array $data, Division $parent = null)
    {
        $this->code       = $data['code'];
        $this->parent     = $parent;
        $this->level      = $data['level'];
        $this->isEnabled  = $data['isEnabled'];
        $this->name       = $data['name'];
        $this->children   = [];

        // If we have some children data, we instancing them
        if (\array_key_exists('children', $data)
            && \is_array($data['children'])
        ) {
            foreach ($data['children'] as $child) {
                $this->addChild(new Division($child, $this));
            }
        }
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Division[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param Division $child
     */
    public function addChild(Division $child): void
    {
        $this->children[] = $child;
    }

    /**
     * @return Division|null
     */
    public function getParent(): ?Division
    {
        return $this->parent;
    }
}
