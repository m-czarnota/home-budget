<?php

declare(strict_types=1);

namespace App\Tests\Category\Stub;

use App\Category\Domain\CategoryDeletionInfo;

class CategoryDeletionInfoStub
{
    public static function createFromArrayData(array $data): CategoryDeletionInfo
    {
        $categoryDeletionInfo = new CategoryDeletionInfo(
            $data['id'],
            $data['canBeDeleted'],
        );

        foreach ($data['subCategories'] ?? [] as $subCategoryData) {
            $categoryDeletionInfo->addSubCategoryDeletionInfo(self::createFromArrayData($subCategoryData));
        }

        return $categoryDeletionInfo;
    }
}
