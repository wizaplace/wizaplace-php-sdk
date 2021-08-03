<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Group;

final class Group
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    public function __construct(array $data)
    {
        $this->id = $data['groupId'];
        $this->name = $data['name'];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
