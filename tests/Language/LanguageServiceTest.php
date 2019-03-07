<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Language;

use Wizaplace\SDK\Cms\CmsService;
use Wizaplace\SDK\Language\LanguageService;
use Wizaplace\SDK\Tests\ApiTestCase;

final class LanguageServiceTest extends ApiTestCase
{
    /**
     * @var LanguageService
     */
    private $languageService;

    public function setUp(): void
    {
        parent::setUp();

        $this->languageService = new LanguageService($this->buildAdminApiClient());
    }

    public function testGetAllLanguages()
    {
        $languages = $this->languageService->getAllLanguages();

        static::assertCount(2, $languages);

        $french = $languages[0];

        static::assertSame('Français', $french->getName());
        static::assertSame('fr', $french->getLangCode());
        static::assertSame('FR', $french->getCountryCode());
        static::assertSame('active', $french->getStatus());
    }
}
