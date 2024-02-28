<?php

namespace App\Tests\Person\Behat;

use App\Person\Domain\PersonNotValidException;
use App\Person\Domain\PersonRepositoryInterface;
use App\Tests\Common\HomeBudgetKernelBrowser;
use App\Tests\Person\Stub\PersonStub;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Step\Given;
use Behat\Step\Then;
use PHPUnit\Framework\Assert;

readonly class PersonContext implements Context
{
    public function __construct(
        private HomeBudgetKernelBrowser $browser,
        private PersonRepositoryInterface $personRepository,
    ) {
    }

    /**
     * @throws PersonNotValidException
     */
    #[Given('there is exist person with')]
    public function allElementsShouldHaveId(PyStringNode $personRawData): void
    {
        $personData = json_decode(trim($personRawData->getRaw()), true);
        $person = PersonStub::createFromArrayData($personData);

        if ($this->personRepository->findOneById($person->id)) {
            return;
        }

        $this->personRepository->add($person);
    }

    #[Then('the response should contains message :message')]
    public function theResponseShouldContainsMessage(string $message): void
    {
        $response = trim($this->browser->getLastResponseContent());
        $responseMessage = json_decode($response);

        Assert::assertEquals($message, $responseMessage);
    }
}
