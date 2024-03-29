<?php

declare(strict_types=1);

namespace App\Tests\Category\Unit\Domain;

use App\Category\Domain\CategoryDeletionInfo;
use PHPUnit\Framework\TestCase;

class CategoryDeletionInfoTest extends TestCase
{
    /**
     * @dataProvider isDeletableDataProvider
     */
    public function testIsDeletable(bool $canBeDeleted, array $subCategoriesCanBeDeleted, array $expectedIsDeletableData): void
    {
        $categoryDeletionInfo = new CategoryDeletionInfo('1', $canBeDeleted);
        foreach ($subCategoriesCanBeDeleted as $index => $subCategoryCanBeDeleted) {
            $subCategoryDeletionInfo = new CategoryDeletionInfo(strval($index), $subCategoryCanBeDeleted);
            $categoryDeletionInfo->addSubCategoryDeletionInfo($subCategoryDeletionInfo);
        }

        self::assertSame($expectedIsDeletableData['isDeletable'], $categoryDeletionInfo->isDeletable());

        $i = 0;
        foreach ($categoryDeletionInfo->getSubCategories() as $subCategoryDeletionInfo) {
            $isSubCategoryDeletable = $expectedIsDeletableData['subCategories'][$i];
            self::assertSame($isSubCategoryDeletable, $subCategoryDeletionInfo->isDeletable());
            ++$i;
        }
    }

    public static function isDeletableDataProvider(): array
    {
        return [
            'category is not deletable due to one of subcategories is not deletable' => [
                'canBeDeleted' => true,
                'subCategoriesCanBeDeleted' => [
                    false,
                    true,
                    true,
                ],
                'expectedIsDeletableData' => [
                    'isDeletable' => false,
                    'subCategories' => [
                        false,
                        true,
                        true,
                    ],
                ],
            ],
            'category is deletable because each category is deletable' => [
                'canBeDeleted' => true,
                'subCategoriesCanBeDeleted' => [
                    true,
                    true,
                    true,
                ],
                'expectedIsDeletableData' => [
                    'isDeletable' => true,
                    'subCategories' => [
                        true,
                        true,
                        true,
                    ],
                ],
            ],
            'category is not deletable even though every category is deletable' => [
                'canBeDeleted' => false,
                'subCategoriesCanBeDeleted' => [
                    true,
                    true,
                    true,
                ],
                'expectedIsDeletableData' => [
                    'isDeletable' => false,
                    'subCategories' => [
                        true,
                        true,
                        true,
                    ],
                ],
            ],
        ];
    }
}
