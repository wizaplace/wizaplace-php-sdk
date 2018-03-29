<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Tax;

use Wizaplace\SDK\AbstractService;

class TaxService extends AbstractService
{
    public function getTaxes(): array
    {
        $this->client->mustBeAuthenticated();
        $taxes = $this->client->get("taxes");

        return array_map(static function ($tax) {
            return new Tax($tax);
        }, $taxes);
    }
}
