<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Language;

final class Language
{
    /** @var string */
    private $name;

    /** @var string */
    private $langCode;

    /** @var string */
    private $countryCode;

    /** @var string */
    private $status;

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->langCode = $data['langCode'];
        $this->countryCode = $data['countryCode'];
        $this->status = $data['status'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLangCode(): string
    {
        return $this->langCode;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
