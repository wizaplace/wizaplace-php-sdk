<?php

namespace Wizaplace\SDK\Organisation;

class OrganisationGroup
{

    private $id;
    private $name;
    private $type;

    public function __construct(array $data = array(), int $flags = 0)
    {
        $this->setId($data['id']);
        $this->setName($data['name']);
        $this->setType($data['type']);
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
     * @return mixed
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }
}
