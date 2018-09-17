<?php

namespace Wizaplace\SDK\Organisation;

use DateTime;

class OrganisationBasket
{

    private $id;
    private $name;
    /**
     * @var boolean
     */
    private $locked;
    /**
     * @var boolean
     */
    private $accepted;
    /**
     * @var DateTime
     */
    private $createdAt;

    public function __construct(array $data = array(), int $flags = 0)
    {
        $this->setId($data['basketId']);
        $this->setName($data['name']);
        $this->setLocked($data['locked']);
        $this->setAccepted($data['accepted']);
        $this->setCreatedAt(DateTime::createFromFormat("c", $data['createdAt']));
    }

    /**
     * @return mixed
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * @param bool $locked
     */
    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    /**
     * @return bool
     */
    public function isAccepted(): bool
    {
        return $this->accepted;
    }

    /**
     * @param bool $accepted
     */
    public function setAccepted(bool $accepted): void
    {
        $this->accepted = $accepted;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
