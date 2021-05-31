<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Subscription;

class SubscriptionActionTrace
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $subscriptionId;

    /** @var null|SubscriptionEventType */
    protected $action;

    /** @var null|\DateTime */
    protected $date;

    /** @var null|int */
    protected $userId;

    /** @var null|string */
    protected $valueBefore;

    /** @var null|string */
    protected $valueAfter;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->subscriptionId = $data['subscription_id'];
        if (\array_key_exists('action', $data) === true
            && $data['action'] !== ''
        ) {
            $this->action = new SubscriptionEventType($data['action']);
        } else {
            $this->action = null;
        }

        if (\array_key_exists('date', $data) === true
            && $data['date'] !== null
        ) {
            $this->date = new \DateTime($data['date']);
        } else {
            $this->date = null;
        }

        $this->userId = $data['userId'] ?? null;
        $this->valueBefore = $data['valueBefore'] ?? null;
        $this->valueAfter = $data['valueAfter'] ?? null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function getAction(): ?SubscriptionEventType
    {
        return $this->action;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getValueBefore(): ?string
    {
        return $this->valueBefore;
    }

    public function getValueAfter(): ?string
    {
        return $this->valueAfter;
    }
}
