<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

/**
 * Class Nationality
 * @package Wizaplace\SDK\User
 */
final class Nationality
{
    /** @var string */
    private $countryCodeA3;

    public function __construct(string $countryCodeA3)
    {
        $this->countryCodeA3 = $countryCodeA3;
    }

    /**
     * @param string $countryCodeA3
     *
     * @return self
     */
    public function setCountryCodeA3(string $countryCodeA3): self
    {
        $this->countryCodeA3 = $countryCodeA3;

        return $this;
    }

    /** @return string */
    public function getCountryCodeA3(): string
    {
        return $this->countryCodeA3;
    }
}
