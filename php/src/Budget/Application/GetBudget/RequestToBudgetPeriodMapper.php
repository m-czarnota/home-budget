<?php

namespace App\Budget\Application\GetBudget;

use App\Budget\Domain\BudgetPeriod;
use App\Budget\Domain\InvalidBudgetPeriodException;
use App\Common\Domain\Period\PeriodUtil;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class RequestToBudgetPeriodMapper
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws InvalidBudgetPeriodException
     */
    public function execute(): BudgetPeriod
    {
        $request = $this->requestStack->getCurrentRequest();
        $year = intval($request->get('year', -1));
        $month = intval($request->get('month', -1));

        if ($year < 0 || $month < 0) {
            throw new BadRequestException("Year and month are required | $month, $year | " . json_encode($_GET));
        }

        return new BudgetPeriod(
            PeriodUtil::startDateOfMonthInYear($month, $year),
            PeriodUtil::endDateOfMonthInYear($month, $year),
        );
    }
}