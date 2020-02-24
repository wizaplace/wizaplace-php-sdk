<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Language;

use Wizaplace\SDK\AbstractService;

final class LanguageService extends AbstractService
{
    /**
     * @return Language[]
     */
    public function getAllLanguages(): array
    {
        $this->client->mustBeAuthenticated();

        return array_map(
            function (array $language): Language {
                return new Language($language);
            },
            $this->client->get('languages')
        );
    }
}
