<?php

declare(strict_types=1);

namespace App\Category\Application\UpdateCategories\Request;

use JsonSerializable;

class RequestCategoryErrorInfoDto implements JsonSerializable
{
    public function __construct(
        public bool $hasError = false,
        public ?string $name = null,
        public ?string $position = null,

        /** @var array<int, RequestCategoryErrorInfoDto> $subCategories */
        public array $subCategories = [],
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'hasError' => $this->hasError,
            'name' => $this->name,
            'position' => $this->position,
        ];

        if (!empty($this->subCategories)) {
            $data = array_merge($data, [
                'subCategories' => $this->subCategories,
            ]);
        }

        return $data;
    }
}
