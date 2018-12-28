<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Division;

use Wizaplace\SDK\User\UserType;

final class Division
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var null|string
     */
    private $parentCode;

    /**
     * @var int
     */
    private $level;

    /**
     * @var bool
     */
    private $isEnabled;

    /**
     * @var string
     */
    private $description;

    /**
     * @var null|int
     */
    private $companyId;

    /**
     * @var null|UserType
     */
    private $disabledBy;

    /**
     * @var null|int
     */
    private $productId;

    /**
     * @var Division[]
     */
    private $children;

    public function __construct(array $data)
    {
        $this->code        = $data['code'];
        $this->parentCode  = $data['parentCode'];
        $this->level       = $data['level'];
        $this->isEnabled   = $data['isEnabled'];
        $this->description = $data['description'];
        $this->companyId   = $data['companyId'] ?? null;
        $this->disabledBy  = $data['disabledBy'] ?? null;
        $this->productId   = $data['productId'] ?? null;
        $this->children    = [];
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return null|string
     */
    public function getParentCode(): ?string
    {
        return $this->parentCode;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getCompanyId(): ?int
    {
        return $this->companyId;
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

    /**
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return $this->productId;
    }

    /**
     * @return Division[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChild(Division $child): void
    {
        $this->children[] = $child;
    }
}
