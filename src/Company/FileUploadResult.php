<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Company;

/**
 * Class FileUploadResult
 * @package Wizaplace\SDK\Company
 */
final class FileUploadResult
{
    /** @var null|string */
    private $errorMessage;

    /**
     * @internal
     *
     * @param string|null $errorMessage
     */
    public function __construct(?string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string|null
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return \is_null($this->errorMessage);
    }
}
