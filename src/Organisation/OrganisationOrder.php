<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Organisation;

/**
 * Class OrganisationOrder
 * @package Wizaplace\SDK\Organisation
 */
class OrganisationOrder
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var string
     */
    private $organisationId;

    /**
     * OrganisationOrder constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setOrderId($data['orderId']);
        $this->setOrganisationId($data['organisationId']);
    }

    /**
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getOrganisationId(): string
    {
        return $this->organisationId;
    }

    /**
     * @param string $organisationId
     */
    public function setOrganisationId(string $organisationId): void
    {
        $this->organisationId = $organisationId;
    }
}
