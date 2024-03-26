<?php

declare(strict_types=1);

namespace App\Category\Infrastructure\ORM;

use App\Category\Domain\Category;
use App\Category\Domain\CategoryRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use ReflectionClass;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DoctrineCategoryRepository extends ServiceEntityRepository implements CategoryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function add(Category $category): void
    {
        $this->getEntityManager()->persist($category);

        foreach ($category->getSubCategories() as $subCategory) {
            $this->addSubCategory($subCategory, $category);
        }
    }

    public function remove(Category $category): void
    {
        foreach ($category->getSubCategories() as $subCategory) {
            if (!$subCategory->isDeleted()) {
                $this->getEntityManager()->remove($subCategory);
            }
        }

        if (!$category->isDeleted()) {
            $this->getEntityManager()->remove($category);
        }
    }

    public function update(Category $category): void
    {
        $existedCategory = $this->find($category->id);
        if (!$existedCategory) {
            throw new InvalidArgumentException("Category {$category->id} doesn't exist");
        }

        $subCategories = $category->getSubCategories(true);

        // adding new subcategories when they don't exist
        foreach ($subCategories as $subCategory) {
            $this->addSubCategory($subCategory, $category);
        }

        // finding original subcategories ids which belong to passed $category
        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare('SELECT id FROM category WHERE parent = :parentId');
        $stmt->bindValue('parentId', $category->id);
        $existedSubCategoryIds = $stmt->executeQuery()->fetchFirstColumn();

        // removing subcategories when they aren't in updated category
        foreach ($existedSubCategoryIds as $existedSubCategoryId) {
            if (isset($subCategories[$existedSubCategoryId])) {
                continue;
            }

            $subCategory = $this->find($existedSubCategoryId);

            // removing subcategory only when it isn't already marked as deleted
            if (!$subCategory->isDeleted()) {
                $this->getEntityManager()->remove($subCategory);
            }
        }
    }

    public function findOneById(string $id): ?Category
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @return array<int, Category>
     */
    public function findList(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->where($qb->expr()->isNull('c.parent'))
            ->orderBy('c.position')
            ->getQuery()
            ->getResult();
    }

    public function findAll(): array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb
            ->where($qb->expr()->isNull('c.parent'))
            ->getQuery()
            ->getResult();
    }

    private function addSubCategory(Category $subCategory, Category $category): void
    {
        // resolve doctrine proxy class problem with reflection and not existed property within this proxy class
        $existedSubCategory = $this->findOneById($subCategory->id);
        if ($existedSubCategory) {
            return;
        }

        $reflectionClass = new ReflectionClass($subCategory);
        $parent = $reflectionClass->getProperty('parent');
        $parent->setAccessible(true);
        $parent->setValue($subCategory, $category);

        $this->getEntityManager()->persist($subCategory);
    }
}
