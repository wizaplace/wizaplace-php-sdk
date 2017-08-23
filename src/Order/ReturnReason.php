<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Order;

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
     */
    public function __construct(int $id, int $position, string $name)
    {
        $this->id = $id;
        $this->position = $position;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function fromApiData($data): self
    {
        return new self(
            $data['id'],
            $data['position'],
            $data['name']
        );
    }
}
