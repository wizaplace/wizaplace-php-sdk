<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

/**
 * Class AttributeVariant
 * @package Wizaplace\SDK\Pim\Attribute
 */
final class AttributeVariant
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['variant_id'];
        $this->name = $data['variant'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
