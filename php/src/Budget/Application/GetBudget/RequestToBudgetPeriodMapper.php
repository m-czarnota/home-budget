<?php

namespace App\Budget\Application\GetBudget;

use App\Budget\Domain\BudgetPeriod;
use App\Budget\Domain\InvalidBudgetPeriodException;
use App\Common\Domain\Period\PeriodUtil;
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
    public function execute(): ?BudgetPeriod
    {
        $request = $this->requestStack->getCurrentRequest();
        $month = intval($request->get('month'));

        if (empty($month)) {
            return null;
        }

        return new BudgetPeriod(
            PeriodUtil::startDateOfMonth($month),
            PeriodUtil::endDateOfMonth($month),
        );
    }
}