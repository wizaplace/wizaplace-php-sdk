<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Tests\Translation;

use Wizaplace\SDK\Tests\ApiTestCase;
use Wizaplace\SDK\Translation\TranslationService;

final class TranslationServiceTest extends ApiTestCase
{
    public function testPushXliffCatalog()
    {
        $this->markTestSkipped('API had a BC break, will be fixed in v0.9');
        $client = $this->buildApiClient();
        $client->authenticate('admin@world-company.com', 'password-admin');

        $xliff = <<<XLIFF
<?xml version="1.0" encoding="utf-8"?>
<xliff xmlns="urn:oasis:names:tc:xliff:document:1.2" version="1.2">
  <file source-language="fr" target-language="fr" datatype="plaintext" original="file.ext">
    <header>
      <tool tool-id="symfony" tool-name="Symfony"/>
    </header>
    <body>
      <trans-unit id="83218ac34c1834c26781fe4bde918ee4" resname="Welcome">
        <source>Welcome</source>
        <target>Bonjour</target>
      </trans-unit>
    </body>
  </file>
</xliff>

XLIFF;

        $translationService = new TranslationService($client);
        $translationService->pushXliffCatalog($xliff, 'fr');
        $catalog = $translationService->getXliffCatalog('fr');

        $this->assertSame($catalog->__toString(), $xliff);
    }
}
