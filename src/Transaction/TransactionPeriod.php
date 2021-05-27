<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Transaction;

final class TransactionPeriod
{
    /** @var \DateTimeImmutable|null */
    private $from;

    /** @var \DateTimeImmutable|null */
    private $to;

    /** @return \DateTimeImmutable|null */
    public function getFrom(): ?\DateTimeImmutable
    {
        return $this->from;
    }

    /** @return \DateTimeImmutable|null */
    public function getTo(): ?\DateTimeImmutable
    {
        return $this->to;
    }

    public function setFrom(?\DateTimeImmutable $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function setTo(?\DateTimeImmutable $to): self
    {
        $this->to = $to;

        return $this;
    }
}
