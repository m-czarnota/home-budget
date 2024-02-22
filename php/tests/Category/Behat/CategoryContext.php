<?php

namespace App\Tests\Category\Behat;

use App\Category\Domain\CategoryNotValidException;
use App\Category\Domain\CategoryRepositoryInterface;
use App\Tests\Category\Stub\CategoryStub;
use App\Tests\Common\HomeBudgetKernelBrowser;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

readonly class CategoryContext implements Context
{
    public function __construct(
        private HomeBudgetKernelBrowser $browser,
        private CategoryRepositoryInterface $categoryRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Then('all elements should have id')]
    public function allElementsShouldHaveId(): void
    {
        $data = $this->browser->getLastResponseContentAsArray();

        foreach ($data as $category) {
            Assert::assertArrayHasKey('id', $category);

            foreach ($category['subCategories'] as $subCategory) {
                Assert::assertArrayHasKey('id', $subCategory);
            }
        }
    }

    #[Then('the response should looks like')]
    public function theResponseShouldLooksLike(PyStringNode $dummyResponse): void
    {
        $dummyResponseFields = json_decode(trim($dummyResponse->getRaw()), true);
        $response = $this->browser->getLastResponseContentAsArray();

        $this->checkIfResponseLooksLikeDummyResponse($response, $dummyResponseFields);
    }

    /**
     * @throws CategoryNotValidException
     */
    #[Given('there are exist categories with')]
    public function thereAreExistCategories(PyStringNode $categoriesContent): void
    {
        $categoriesData = json_decode(trim($categoriesContent->getRaw()), true);

        foreach ($categoriesData as $categoryData) {
            $existedCategory = $this->categoryRepository->findOneById($categoryData['id']);
            if ($existedCategory !== null) {
                continue;
            }

            $category = CategoryStub::createExampleCategory($categoryData['id'], $categoryData['name']);
            $this->categoryRepository->add($category);
        }

        $this->entityManager->flush();
    }

    private function checkIfResponseLooksLikeDummyResponse(array $response, array $dummyResponse): void
    {
        $specialNonExistedDataInResponse = '!not exists@';

        foreach ($dummyResponse as $dummyResponseIndex => $dummyResponseData) {
            $responseData = array_key_exists($dummyResponseIndex, $response)
                ? $response[$dummyResponseIndex]
                : $specialNonExistedDataInResponse;

            if (is_bool($responseData)) {
                Assert::assertEquals($specialNonExistedDataInResponse, $responseData);
            } else {
                Assert::assertNotEquals($specialNonExistedDataInResponse, $responseData);
            }
            Assert::assertEquals(gettype($dummyResponseData), gettype($responseData));

            if (is_array($dummyResponseData)) {
                $this->checkIfResponseLooksLikeDummyResponse($responseData, $dummyResponseData);
            }
        }
    }
}
