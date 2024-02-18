<?php

namespace App\Category\Application;

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
