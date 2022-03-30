<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Transaction;

class Transaction
{
    /** @var string */
    protected $id;

    /** @var string|null */
    protected $transactionReference;

    /** @var TransactionType */
    protected $type;

    /** @var TransactionStatus */
    protected $status;

    /** @var float */
    protected $amount;

    /** @var string|null */
    protected $processorName;

    /** @var array|null */
    protected $processorInformation;

    /** @var null|\DateTime */
    protected $createdAt;

    /** @var string|null */
    protected $origin;

    /** @var string|null */
    protected $destination;

    /** @var string|null */
    protected $currency;

    /** @var int|null */
    protected $orderId;

    public function __construct(array $data)
    {
        $this->id = $data['transactionId'];
        $this->transactionReference = $data['transactionReference'];
        $this->type = new TransactionType($data['type']);
        $this->status = new TransactionStatus($data['status']);
        $this->amount = $data['amount'];
        $this->processorName = $data['processorName'];
        $this->processorInformation = $data['processorInformation'];
        if (\array_key_exists('transactionDate', $data) === true
            && $data['transactionDate'] !== null
        ) {
            $this->createdAt = new \DateTime($data['transactionDate']);
        } else {
            $this->createdAt = null;
        }
        $this->origin = $data['origin'] ?? null;
        $this->destination = $data['destination'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->orderId = $data['order_id'] ?? null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTransactionReference(): ?string
    {
        return $this->transactionReference;
    }

    public function getType(): TransactionType
    {
        return $this->type;
    }

    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getProcessorName(): ?string
    {
        return $this->processorName;
    }

    public function getProcessorInformation(): ?array
    {
        return $this->processorInformation;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }
}
