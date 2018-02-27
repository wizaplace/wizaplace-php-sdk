<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Image;

final class Image implements \JsonSerializable
{
    /** @var int */
    private $id;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
        ];
    }
}
