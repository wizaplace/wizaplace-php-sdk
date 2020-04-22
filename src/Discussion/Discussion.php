<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Discussion;

use function theodorejb\polycast\to_int;

/**
 * Class Discussion
 * @package Wizaplace\SDK\Discussion
 */
final class Discussion
{
    /** @var int */
    private $id;

    /** @var string */
    private $recipient;

    /** @var int */
    private $productId;

    /** @var string */
    private $title;

    /** @var int */
    private $unreadCount;

    /**
     * @internal
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->recipient = $data['recipient'];
        $this->productId = to_int($data['productId'] ?? 0);
        $this->title = $data['title'];
        $this->unreadCount = $data['unreadCount'];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRecipient(): string
    {
        return $this->recipient;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getUnreadCount(): int
    {
        return $this->unreadCount;
    }
}
