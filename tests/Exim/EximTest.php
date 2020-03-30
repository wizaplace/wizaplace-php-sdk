<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Tests\Exim;

use Wizaplace\SDK\Exim\EximService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class EximTest extends ApiTestCase
{
    public function testImportProducts()
    {
        $apiClient = $this->buildApiClient();
        $apiClient->authenticate('vendor@wizaplace.com', 'password');

        $eximService = new EximService($apiClient);
        $jobId = $eximService->importProducts('tests/Exim/importProductExample.csv');
        static::assertNotEmpty($jobId);
    }
}
