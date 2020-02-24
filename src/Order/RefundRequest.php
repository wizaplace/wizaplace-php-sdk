<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Order;

use Wizaplace\SDK\ArrayableInterface;

final class RefundRequest implements ArrayableInterface
{
    /** @var bool */
    private $isPartial;

    /** @var RefundRequestItem[]|null */
    private $items;

    /** @var RefundRequestShipping|null */
    private $shipping;

    /** @var string|null */
    private $message;

    /** @var string|null */
    private $creditNoteReference;

    /** @param RefundRequestItem[]|null $items */
    public function __construct(
        bool $isPartial = null,
        array $items = null,
        RefundRequestShipping $shipping = null,
        string $message = null,
        string $creditNoteReference = null
    ) {
        $this->isPartial = $isPartial === true;
        $this->items = $items;
        $this->shipping = $shipping;
        $this->message = $message;
        $this->creditNoteReference = $creditNoteReference;
    }

    public function isPartial(): bool
    {
        return $this->isPartial;
    }

    public function setIsPartial(?bool $isPartial): self
    {
        $this->isPartial = $isPartial === true;

        return $this;
    }

    /** @return RefundRequestItem[]|null */
    public function getItems(): ?array
    {
        return $this->items;
    }

    /** @param RefundRequestItem[]|null $items */
    public function setItems(?array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getShipping(): ?RefundRequestShipping
    {
        return $this->shipping;
    }

    public function setShipping(?RefundRequestShipping $shipping): self
    {
        $this->shipping = $shipping;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCreditNoteReference(): ?string
    {
        return $this->creditNoteReference;
    }

    public function setCreditNoteReference(?string $creditNoteReference): self
    {
        $this->creditNoteReference = $creditNoteReference;

        return $this;
    }

    /** @return mixed[] */
    public function toArray(): array
    {
        return [
            'isPartial' => $this->isPartial,
            'items' => array_map(function (RefundRequestItem $item): array {
                return $item->toArray();
            }, $this->items ?? []),
            'shipping' => $this->shipping ? $this->shipping->toArray() : null,
            'message' => $this->message,
            'creditNoteReference' => $this->creditNoteReference,
        ];
    }
}
