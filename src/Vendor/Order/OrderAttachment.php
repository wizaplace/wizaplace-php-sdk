<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Order;

class OrderAttachment
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var OrderAttachmentType */
    private $type;

    /** @var string */
    private $filename;

    /** @var string */
    private $url;

    /** @var \DateTimeInterface */
    private $createdAt;

    /** @var \DateTimeInterface */
    private $updatedAt;

    /** @var int */
    private $createdBy;

    /** @var int */
    private $updatedBy;

    public function __construct(array $attachmentData)
    {
        $this->id = $attachmentData['id'];
        $this->name = $attachmentData['name'];
        $this->type = new OrderAttachmentType($attachmentData['type']);
        $this->filename = $attachmentData['filename'];
        $this->url = $attachmentData['url'];
        $this->createdAt = new \DateTime($attachmentData['createdAt']);
        $this->updatedAt = new \DateTime($attachmentData['updatedAt']);
        $this->createdBy = $attachmentData['createdBy'];
        $this->updatedBy = $attachmentData['updatedBy'];
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): OrderAttachmentType
    {
        return $this->type;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): int
    {
        return $this->updatedBy;
    }
}
