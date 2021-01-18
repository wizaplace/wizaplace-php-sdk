<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Discussion;

/**
 * Class MessageAttachment
 * @package Wizaplace\SDK\Discussion
 */
final class MessageAttachment
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var int */
    private $messageId;

    /** @var string|null */
    private $publicUrl;

    /**
     * @internal
     *
     * @param array $data
     *
     * @throws \Exception
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->messageId = $data['messageId'];
        $this->publicUrl = $data['publicUrl'];
    }

    /** @return string */
    public function getId(): string
    {
        return $this->id;
    }

    /** @return string */
    public function getName(): string
    {
        return $this->name;
    }

    /** @return int */
    public function getMessageId(): int
    {
        return $this->messageId;
    }

    /** @return string */
    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }
}
