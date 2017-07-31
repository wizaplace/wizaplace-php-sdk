<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Company;

class FiledUploadResult
{
    /** @var null|string */
    private $errorMessage;

    public function __construct(?string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function isSuccess(): bool
    {
        return is_null($this->errorMessage);
    }
}
