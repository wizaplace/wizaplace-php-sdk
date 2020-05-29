<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Division;

/**
 * Manage filters used in an API get divisions-tree request
 */
class DivisionsTreeFilters
{
    /** @var bool|null */
    private $isEnabled;

    /** @var string|null */
    private $rootCode;

    public function setIsEnabled(?bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function getIsEnabledToString(): ?string
    {
        return $this->isEnabled === true ? 'true' : 'false';
    }

    public function getRootCode(): ?string
    {
        return $this->rootCode;
    }

    public function setRootCode(?string $rootCode): self
    {
        $this->rootCode = $rootCode;

        return $this;
    }

    /**
     * Expose filter items which have been setted
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return array_filter(
            [
                'isEnabled' => \is_bool($this->isEnabled())
                    ? $this->getIsEnabledToString()
                    : null
                ,
                'rootCode' => $this->getRootCode(),
            ],
            function ($item): bool {
                return \is_null($item) === false;
            }
        );
    }
}
