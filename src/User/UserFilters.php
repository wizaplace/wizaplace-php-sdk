<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

class UserFilters
{
    /** @var string|null */
    private $name;

    /** @var string|null */
    private $email;

    /** @var string|null */
    private $phone;

    /** @var string|null */
    private $external;

    /** @var string|null */
    private $loyalty;

    /** @var bool|null */
    private $isProfessional;

    /** @var int|null */
    private $companyId;

    /** @var int|null */
    private $elements;

    /** @var int */
    private $page;

    /** @var string[]|null */
    private $type;

    public function __construct(array $data)
    {
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->external = $data['external'] ?? null;
        $this->loyalty = $data['loyalty'] ?? null;
        $this->isProfessional = $data['isProfessional'] ?? null;
        $this->companyId = $data['companyId'] ?? null;
        $this->elements = $data['elements'] ?? null;
        $this->page = $data['page'] ?? 0;
        $this->type = $data['type'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getExternal(): ?string
    {
        return $this->external;
    }

    public function setExternal(?string $external): self
    {
        $this->external = $external;

        return $this;
    }

    public function getLoyalty(): ?string
    {
        return $this->loyalty;
    }

    public function setLoyalty(?string $loyalty): self
    {
        $this->loyalty = $loyalty;

        return $this;
    }

    public function getIsProfessional(): ?bool
    {
        return $this->isProfessional;
    }

    public function setIsProfessional(?bool $isProfessional): self
    {
        $this->isProfessional = $isProfessional;

        return $this;
    }

    /** @return int|null */
    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function setCompanyId(?int $companyId): self
    {
        $this->companyId = $companyId;

        return $this;
    }

    /** @return string[]|null */
    public function getType(): ?array
    {
        return $this->type;
    }

    /** @param string[]|null $type */
    public function setType(?array $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getElements(): ?int
    {
        return $this->elements;
    }

    public function setElements(?int $elements): self
    {
        $this->elements = $elements;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }

    /** @return mixed[] */
    public function serialize(): array
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone(),
            'external' => $this->getExternal(),
            'loyalty' => $this->getLoyalty(),
            'isProfessional' => $this->getIsProfessional(),
            'companyId' => $this->getCompanyId(),
            'elements' => $this->getElements(),
            'page' => $this->getPage(),
            'type' => $this->getType()
        ];
    }
}
