<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Division;

use Wizaplace\SDK\User\UserType;

final class DivisionCompany
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var null|UserType
     */
    private $disabledBy;

    public function __construct(array $data)
    {
        $this->code       = $data['code'];
        $this->isEnabled  = $data['is_enabled'];
        $this->disabledBy = $data['disabled_by'] ?? null;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return null|UserType
     */
    public function getDisabledBy(): ?UserType
    {
        if (is_null($this->disabledBy)) {
            return null;
        }

        return ($this->disabledBy === UserType::ADMIN()->getValue()) ? UserType::ADMIN() : UserType::VENDOR();
    }
}
