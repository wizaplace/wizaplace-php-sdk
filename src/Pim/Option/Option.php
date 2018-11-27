<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Option;

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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->id = $data['option_id'];
        $this->name = $data['option_name'];
        $this->status = new OptionStatus($data['status']);
        $this->variants = array_map(function (array $variantData): OptionVariant {
            return new OptionVariant($variantData);
        }, $data['variants']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): OptionStatus
    {
        return $this->status;
    }

    public function isEnabled(): bool
    {
        return $this->status->getKey() === OptionStatus::ENABLED()->getKey();
    }

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
}
