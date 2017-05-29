<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\User;

class User
{
    /** @var int */
    private $id;
    /** @var string */
    private $email;
    /** @var string */
    private $firstname;
    /** @var string */
    private $lastname;
    /** @var array */
    private $billingAddress;
    /** @var array */
    private $shippingAddress;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->email = $data['email'];
        $this->firstname = $data['firstName'];
        $this->lastname = $data['lastName'];
        $this->billingAddress = $data['addresses']['billing'];
        $this->shippingAddress = $data['addresses']['shipping'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getBillingAddress(): array
    {
        return $this->billingAddress;
    }

    public function getShippingAddress(): array
    {
        return $this->shippingAddress;
    }
}
