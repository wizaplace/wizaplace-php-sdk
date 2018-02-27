<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog\Facet;

use function theodorejb\polycast\to_string;

abstract class Facet implements \JsonSerializable
{
    /** @var string */
    private $name;
    /** @var string */
    private $label;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->name = to_string($data['name']); // string cast is necessary because the API sometimes sends IDs as integers
        $this->label = $data['label'];
    }

    public static function buildFromJson(array $data): self
    {
        if ($data['isNumeric']) {
            return new NumericFacet($data);
        }

        return new ListFacet($data);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
        ];
    }
}
