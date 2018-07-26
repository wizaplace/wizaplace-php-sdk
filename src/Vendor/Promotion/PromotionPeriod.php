<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion;

final class PromotionPeriod
{
    /** @var \DateTimeImmutable */
    private $from;

    /** @var \DateTimeImmutable */
    private $to;

    public function __construct(\DateTimeInterface $from, \DateTimeInterface $to)
    {
        if ($from->diff($to)->invert === 1) {
            throw new \Exception('promotion cannot start after it ends');
        }

        $this->from = self::convertDateTimeInterfaceToImmutable($from);
        $this->to = self::convertDateTimeInterfaceToImmutable($to);
    }

    public function getFrom(): \DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): \DateTimeImmutable
    {
        return $this->to;
    }

    private static function convertDateTimeInterfaceToImmutable(\DateTimeInterface $dateTime): \DateTimeImmutable
    {
        if ($dateTime instanceof \DateTimeImmutable) {
            return $dateTime;
        }
        if ($dateTime instanceof \DateTime) {
            return \DateTimeImmutable::createFromMutable($dateTime);
        }

        return new \DateTimeImmutable('@'.$dateTime->getTimestamp());
    }
}
