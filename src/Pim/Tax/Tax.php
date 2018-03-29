<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Tax;

class Tax
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    public function __construct(array $data)
    {
        $this->id = $data['tax_id'];
        $this->name = $data['tax'];
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
