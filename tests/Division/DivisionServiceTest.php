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

    public function testSetDivision()
    {
        $divisionService = $this->buildDivisionService('admin@wizaplace.com', 'password');

        /** @var Division[] $result */
        $result = $divisionService->set("FR-42", true);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Division::class, $result[0]);
        $this->assertTrue($result[0]->isEnabled());

        /** @var Division[] $result */
        $result = $divisionService->set("FR-42", false);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Division::class, $result[0]);
        $this->assertFalse($result[0]->isEnabled());
    }

    private function buildDivisionService(string $email = null, string $password = null)
    {
        $apiClient = $this->buildApiClient();
        if (!is_null($email) && !is_null($password)) {
            $apiClient->authenticate($email, $password);
        }

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
