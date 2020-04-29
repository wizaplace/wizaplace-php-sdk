<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Division;

final class DivisionSettings
{
    /** @var array */
    private $included;

    /** @var array */
    private $excluded;

    /** @param mixed[] $data */
    public function __construct(array $data)
    {
        $this->setIncluded($data['included']);
        $this->setExcluded($data['excluded']);
    }

    /** @return string[] */
    public function getIncluded(): array
    {
        return $this->included;
    }

    /** @param string[] $included */
    public function setIncluded(array $included): self
    {
        $this->included = $included;

        return $this;
    }

    /** @return string[] */
    public function getExcluded(): array
    {
        return $this->excluded;
    }

    /** @param string[] $excluded */
    public function setExcluded(array $excluded): self
    {
        $this->excluded = $excluded;

        return $this;
    }
}
