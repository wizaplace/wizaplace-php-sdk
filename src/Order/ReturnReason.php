<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

/**
 * Class ReturnReason
 * @package Wizaplace\SDK\Order
 */
final class ReturnReason
{
    /** @var int */
    private $id;
    /** @var int */
    private $position;
    /** @var string */
    private $name;

    /**
     * @internal
     *
     * @param int    $id
     * @param int    $position
     * @param string $name
     */
    public function __construct(int $id, int $position, string $name)
    {
        $this->id = $id;
        $this->position = $position;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param array $data
     *
     * @return ReturnReason
     */
    public static function fromApiData(array $data): self
    {
        return new self(
            $data['id'],
            $data['position'],
            $data['name']
        );
    }
}
