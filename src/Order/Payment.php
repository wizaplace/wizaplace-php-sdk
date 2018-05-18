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

    /**
     * @internal
     */
    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->processorName = $data['processorName'];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getProcessorName(): ?string
    {
        return $this->processorName;
    }
}
