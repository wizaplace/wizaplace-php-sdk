<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

use Wizaplace\SDK\Price;

class SubscriptionSummary
{
    /** @var string */
    private $id;

    /** @var int */
    private $userId;

    /** @var int */
    private $companyId;

    /** @var string */
    private $cardId;

    /** @var int */
    private $firstOrderId;

    /** @var string */
    private $name;

    /** @var SubscriptionStatus */
    private $status;

    /** @var Price */
    private $price;

    /** @var bool */
    private $isAutorenew;

    /** @var int */
    private $commitmentPeriod;

    /** @var int */
    private $paymentFrequency;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime */
    private $nextPaymentAt;

    /** @var \DateTime */
    private $commitmentEndAt;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->userId = $data['userId'];
        $this->companyId = $data['companyId'];
        $this->cardId = $data['cardId'];
        $this->firstOrderId = $data['firstOrderId'];
        $this->name = $data['name'];
        $this->status = SubscriptionStatus::stringToStatus($data['status']);
        $this->price = new Price($data['price']);
        $this->isAutorenew = $data['isAutorenew'];
        $this->commitmentPeriod = $data['commitmentPeriod'];
        $this->paymentFrequency = $data['paymentFrequency'];
        $this->createdAt = new \DateTime($data['createdAt']);
        $this->nextPaymentAt = $data['nextPaymentAt'];
        $this->commitmentEndAt = $data['commitmentEndAt'];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCompanyId(): int
    {
        return $this->companyId;
    }

    public function getCardId(): string
    {
        return $this->cardId;
    }

    public function getFirstOrderId(): int
    {
        return $this->firstOrderId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): SubscriptionStatus
    {
        return $this->status;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function isAutorenew(): bool
    {
        return $this->isAutorenew;
    }

    public function getCommitmentPeriod(): int
    {
        return $this->commitmentPeriod;
    }

    public function getPaymentFrequency(): int
    {
        return $this->paymentFrequency;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getNextPaymentAt(): \DateTime
    {
        return $this->nextPaymentAt;
    }

    public function getCommitmentEndAt(): \DateTime
    {
        return $this->commitmentEndAt;
    }
}
