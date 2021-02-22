<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

final class AttachmentsOrder
{
    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $filename;

    /** @var string */
    private $url;

    /** @var OrderAttachmentType */
    private $type;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->filename = $data['filename'];
        $this->url = $data['url'];
        $this->type = new OrderAttachmentType($data['type']);
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
}
