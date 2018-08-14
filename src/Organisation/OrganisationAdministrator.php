<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Organisation;

use Symfony\Component\Validator\Constraints\Uuid;
use function theodorejb\polycast\to_string;

class OrganisationAdministrator
{
    /** @var string */
    private $email;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $password;

    /** @var string */
    private $title;

    /** @var string */
    private $occupation;

    public function __construct(array $data)
    {
        $this->email = to_string($data['email']);
        $this->firstName = to_string($data['firstName']);
        $this->lastName = to_string($data['lastName']);
        $this->password = to_string($data['password']);
        $this->title = to_string($data['title']);
        $this->occupation = to_string($data['occupation']);
    }

    public function toArray()
    {
        $data = [];

        $data['email'] = isset($this->email) ? $this->email : '';
        $data['firstName'] = isset($this->firstName) ? $this->firstName : '';
        $data['lastName'] = isset($this->lastName) ? $this->lastName : '';
        $data['password'] = isset($this->password) ? $this->password : '';
        $data['title'] = isset($this->title) ? $this->title : '';
        $data['occupation'] = isset($this->occupation) ? $this->occupation : '';

        return $data;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getOccupation(): string
    {
        return $this->occupation;
    }

    /**
     * @param string $occupation
     */
    public function setOccupation(string $occupation): void
    {
        $this->occupation = $occupation;
    }
}
