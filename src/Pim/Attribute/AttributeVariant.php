<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

final class AttributeVariant
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['variant_id'];
        $this->name = $data['variant'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
