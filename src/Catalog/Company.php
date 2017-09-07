<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

final class Company
{
    /** @var integer */
    private $id;

    /** @var string */
    private $name;

    /**
     * @internal
     */
    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
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
