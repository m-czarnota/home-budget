<?php

declare(strict_types=1);

namespace App\Category\Application\UpdateCategories;

use App\Category\Application\UpdateCategories\Request\RequestCategoryErrorInfoDto;

class ResponseErrorDto
{
    public readonly bool $isError;

    public function __construct(
        /** @var array<int, RequestCategoryErrorInfoDto> $errors */
        public array $errors = [],
    ) {
        $this->isError = true;
    }
}
