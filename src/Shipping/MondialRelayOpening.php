<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Shipping;

use function theodorejb\polycast\to_int;

class MondialRelayOpening
{
    /**
     * Day of the week. 0 = Monday
     *
     * @var int
     */
    private $day;

    /**
     * The hour at which the relay point opens for the 1st time interval of the day.
     * Ex: 0930
     *
     * @var string
     */
    private $openingHour1; // phpcs:ignore

    /**
     * The hour at which the relay point closes for the 1st time interval of the day.
     *
     * @var string
     */
    private $openingHour2; // phpcs:ignore

    /**
     * The hour at which the relay point opens for the 2nd time interval of the day.
     *
     * @var string
     */
    private $openingHour3; // phpcs:ignore

    /**
     * The hour at which the relay point closes for the 2nd time interval of the day.
     *
     * @var string
     */
    private $openingHour4; // phpcs:ignore

    public function __construct(array $data)
    {
        $this->day = to_int($data['day']);
        $this->openingHour1 = $data['openingHour1'];
        $this->openingHour2 = $data['openingHour2'];
        $this->openingHour3 = $data['openingHour3'];
        $this->openingHour4 = $data['openingHour4'];
    }

    public function getDay(): int
    {
        return $this->day;
    }

    public function getOpeningHour1(): string
    {
        return $this->openingHour1;
    }

    public function getOpeningHour2(): string
    {
        return $this->openingHour2;
    }

    public function getOpeningHour3(): string
    {
        return $this->openingHour3;
    }

    public function getOpeningHour4(): string
    {
        return $this->openingHour4;
    }
}
