<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

final class Facet
{
    /** @var string */
    private $name;
    /** @var string */
    private $label;
    /** @var bool */
    private $isNumeric;
    /** @var array */
    private $values;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->label = $data['label'];
        $this->isNumeric = $data['isNumeric'];
        $this->values = $data['values'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isIsNumeric(): bool
    {
        return $this->isNumeric;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
