<?php

/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Tax;

/**
 * Class Tax
 * @package Wizaplace\SDK\Pim\Tax
 */
final class Tax
{
    /** @var int|null */
    private $id;

    /** @var int */
    private $tax_id;

    /** @var int|null */
    private $priority;

    /** @var string|null */
    private $code;

    /** @var bool|null */
    private $isEnabled;

    /** @var string|null */
    private $tax;

    /** @var string */
    private $name;

    /** @var float|null */
    private $rate;

    /** @internal
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->tax_id = $data['tax_id'];
        $this->priority = $data['priority'] ?? null;
        $this->code = $data['code'] ?? null;
        $this->isEnabled = $data['is_enabled'] ?? null;
        $this->tax = $data['tax'] ?? null;
        $this->name = $data['name'];
        $this->rate = $data['rate'] ?? null;
    }

    /** @return int|null */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** @return int */
    public function getTaxId(): int
    {
        return $this->tax_id;
    }

    /** @return int|null */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /** @return string|null */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /** @return bool|null */
    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    /** @return string|null */
    public function getTax(): ?string
    {
        return $this->tax;
    }

    /** @return string|null */
    public function getName(): ?string
    {
        return $this->name;
    }

    /** @return float|null */
    public function getRate(): ?float
    {
        return $this->rate;
    }
}
