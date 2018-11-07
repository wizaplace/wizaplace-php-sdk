<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
namespace Wizaplace\SDK\Tests\Division;

use Wizaplace\SDK\Division\Division;
use Wizaplace\SDK\Division\DivisionService;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Tests\ApiTestCase;

class DivisionServiceTest extends ApiTestCase
{
    public function testGetAllDivisions()
    {
        $divisionService = $this->buildDivisionService();

        $divisions = $divisionService->get();

        foreach ($divisions as $division) {
            $this->recursiveTest($division);
        }
    }

    public function testGetDivisionNotFound()
    {
        $divisionService = $this->buildDivisionService();

        $this->expectException(NotFound::class);
        $divisionService->get("TEST");
    }

    private function buildDivisionService()
    {
        $apiClient = $this->buildApiClient();

        return new DivisionService($apiClient);
    }

    private function recursiveTest(Division $division)
    {
        $this->assertInstanceOf(Division::class, $division);

        if (!empty($division->getChildren())) {
            foreach ($division->getChildren() as $child) {
                $this->recursiveTest($child);
            }
        }
    }
}
