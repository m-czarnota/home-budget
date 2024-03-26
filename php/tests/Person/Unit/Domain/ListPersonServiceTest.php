<?php

declare(strict_types=1);

namespace App\Tests\Person\Unit\Domain;

use App\Person\Domain\ListPersonService;
use App\Person\Domain\PersonNotValidException;
use App\Person\Domain\PersonRepositoryInterface;
use App\Tests\Person\Stub\PersonStub;
use PHPUnit\Framework\TestCase;

class ListPersonServiceTest extends TestCase
{
    private readonly PersonRepositoryInterface $personRepository;
    private readonly ListPersonService $service;

    protected function setUp(): void
    {
        $this->personRepository = $this->createMock(PersonRepositoryInterface::class);
        $this->service = new ListPersonService(
            $this->personRepository,
        );
    }

    /**
     * @dataProvider executeDataProvider
     *
     * @throws PersonNotValidException
     */
    public function testExecute(array $dataFromDb): void
    {
        $peopleInDb = PersonStub::createMultipleFromArrayData($dataFromDb);
        $this->personRepository
            ->method('findList')
            ->willReturn($peopleInDb);

        $people = $this->service->execute();

        self::assertCount(count($dataFromDb), $people);

        foreach ($people as $personIndex => $person) {
            $personDataInDb = $dataFromDb[$personIndex];

            self::assertEquals($personDataInDb['id'], $person->id);
            self::assertEquals($personDataInDb['name'], $person->name);
        }
    }

    public static function executeDataProvider(): array
    {
        return [
            'no people from db' => [
                'dataFromDb' => [],
            ],
            'two people from db' => [
                'dataFromDb' => [
                    [
                        'id' => '1',
                        'name' => 'Michal',
                        'lastModified' => '2024-02-13 14:54:23',
                    ],
                    [
                        'id' => '2',
                        'name' => 'Asia',
                        'lastModified' => '2024-02-13 14:55:12',
                    ],
                ],
            ],
        ];
    }
}
