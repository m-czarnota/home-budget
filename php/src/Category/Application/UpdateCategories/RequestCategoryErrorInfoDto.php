<?php

namespace App\Category\Application\UpdateCategories;

class RequestCategoryErrorInfoDto
{
    public function __construct(
        public bool $hasError = false,
        public ?string $name = null,
        public ?string $position = null,

        /** @var array<int, RequestCategoryErrorInfoDto> $subCategories */
        public array $subCategories = [],
    ) {
    }
}
