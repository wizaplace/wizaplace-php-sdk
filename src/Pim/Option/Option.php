<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Option;

/**
 * Class Option
 * @package Wizaplace\SDK\Pim\Option
 */
final class Option
{
    /** @var int */
    private $id;

    /** @var string */
    private $name;

    /** @var OptionVariant[] */
    private $variants;

    /** @var OptionStatus */
    private $status;

    /** @var bool */
    private $isSystem;

    /** @var null|string */
    private $code;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['option_id'];
        $this->name = $data['option_name'];
        $this->status = new OptionStatus($data['status']);
        $this->variants = array_map(function (array $variantData): OptionVariant {
            return new OptionVariant($variantData);
        }, $data['variants']);
        $this->isSystem = $data['is_system'] ?? false;
        $this->code = $data['code'] ?? null;
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

    /**
     * @return OptionStatus
     */
    public function getStatus(): OptionStatus
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->status->getKey() === OptionStatus::ENABLED()->getKey();
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->status->getKey() === OptionStatus::DISABLED()->getKey();
    }

    /**
     * @return OptionVariant[]
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    /**
     * @return bool
     */
    public function isSystem(): bool
    {
        return $this->isSystem;
    }

    /**
     * @return null|string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }
}
