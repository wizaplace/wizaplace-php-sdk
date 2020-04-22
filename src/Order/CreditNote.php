<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

final class CreditNote
{
    /** @var int */
    private $refundId;

    /** @var string */
    private $filename;

    /** @var string */
    private $url;

    public function __construct(array $data)
    {
        $this->refundId = $data['refundId'] ?? null;
        $this->filename = $data['filename'] ?? null;
        $this->url = $data['url'] ?? null;
    }

    public function getRefundId(): int
    {
        return $this->refundId;
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
