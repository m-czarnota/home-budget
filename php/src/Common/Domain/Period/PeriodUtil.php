<?php

namespace App\Common\Domain\Period;

use DateTime;
use DateTimeInterface;

class PeriodUtil
{
    public static function startDateOfMonthInYear(int $month, int $year): DateTimeInterface
    {
        $startDate = new DateTime();
        $startDate
            ->setDate($year, $month, 1)
            ->setTime(0, 0, 0, 0);

        return $startDate;
    }

    public static function endDateOfMonthInYear(int $month, int $year): DateTimeInterface
    {
        $startDate = self::startDateOfMonthInYear($month, $year);
        $startDate->modify('last day of this month');

        return $startDate;
    }
}