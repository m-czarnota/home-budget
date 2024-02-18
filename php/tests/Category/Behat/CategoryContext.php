<?php

namespace App\Tests\Category\Behat;

use App\Tests\Common\HomeBudgetKernelBrowser;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Then;
use PHPUnit\Framework\Assert;

readonly class CategoryContext implements Context
{
    public function __construct(
        private HomeBudgetKernelBrowser $browser,
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
