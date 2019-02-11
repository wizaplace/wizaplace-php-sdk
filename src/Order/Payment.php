<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Order;

class Payment
{
    /** @var string */
    private $type;
    /** @var string|null */
    private $processorName;
    /** @var \DateTimeImmutable|null */
    private $commitmentDate;
    /** @var array */
    private $processorInformations;

    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->processorName = $data['processorName'] ?? $data['processor_name'] ?? null;
        $this->commitmentDate = (isset($data['commitmentDate'])) ? new \DateTimeImmutable($data['commitmentDate']) : null;
        $this->processorInformations = $data['processorInformation'] ?? [];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProcessorName(): ?string
    {
        return $this->processorName;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCommitmentDate(): ?\DateTimeImmutable
    {
        return $this->commitmentDate;
    }

    /**
     * @return array
     */
    public function getProcessorInformations(): array
    {
        return $this->processorInformations;
    }
}
