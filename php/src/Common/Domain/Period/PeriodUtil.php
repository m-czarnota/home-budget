<?php

namespace App\Common\Domain\Period;

use DateTime;
use DateTimeInterface;

class PeriodUtil
{
    public static function startDateOfMonth(int $month): DateTimeInterface
    {
        $startDate = new DateTime();
        $startDate
            ->setDate($startDate->format('Y'), $month, 1)
            ->setTime(0, 0, 0, 0);

        return $startDate;
    }

    public static function endDateOfMonth(int $month): DateTimeInterface
    {
        $startDate = self::startDateOfMonth($month);
        $startDate->modify('last day of this month');

        return $startDate;
    }
}