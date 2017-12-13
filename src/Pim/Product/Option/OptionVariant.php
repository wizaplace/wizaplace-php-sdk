<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\Option;

final class OptionVariant
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
        $this->name = $data['variant_name'];
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
