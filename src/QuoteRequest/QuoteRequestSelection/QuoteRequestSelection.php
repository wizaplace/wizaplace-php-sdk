<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\QuoteRequest\QuoteRequestSelection;

class QuoteRequestSelection
{
    /** @var int */
    private $id;

    /** @var int */
    private $userId;

    /** @var bool */
    private $active;

    /** @var string[] */
    private $declinationIds;

    /** @var int[] */
    private $quoteRequestsIds;

    /** @var \DateTime */
    private $createdAt;

    /** @var \DateTime|null */
    private $updatedAt;

    /** @param mixed[] $data */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->userId = $data['userId'];
        $this->active = $data['active'];
        $this->declinationIds = $data['declinationIds'];
        $this->quoteRequestsIds = $data['quoteRequestsIds'];
        $this->createdAt = (new \DateTime())->setTimestamp($data['createdAt']);
        $this->updatedAt = $data['updatedAt'] === ""
            ? null
            : (new \DateTime())->setTimestamp($data['updatedAt']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /** @return string[] */
    public function getDeclinationIds(): array
    {
        return $this->declinationIds;
    }

    /** @return int[] */
    public function getQuoteRequestsIds(): array
    {
        return $this->quoteRequestsIds;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
}
