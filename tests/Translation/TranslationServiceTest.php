<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Tests\Translation;

use Wizaplace\Tests\ApiTestCase;
use Wizaplace\Translation\TranslationService;

class TranslationServiceTest extends ApiTestCase
{
    public function testPushXliffCatalog()
    {
        $client = $this->buildApiClient();
        $client->authenticate("admin@wizaplace.com", "password");

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
