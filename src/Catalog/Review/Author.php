<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Review;

/**
 * Class Author
 * @package Wizaplace\SDK\Catalog\Review
 */
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

    /**
     * Author constructor.
     *
     * @param string      $name
     * @param int|null    $id
     * @param string|null $email
     */
    public function __construct(string $name, ?int $id = null, ?string $email = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
