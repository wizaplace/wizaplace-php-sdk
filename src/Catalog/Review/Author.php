<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Catalog\Review;

final class Author
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int|null
     */
    private $id;

    /**
     * @var null|string
     */
    private $email;

    public function __construct(string $name, ?int $id = null, ?string $email = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->email = $email;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
