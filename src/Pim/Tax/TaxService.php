<?php
/**
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Tax;

use Wizaplace\SDK\AbstractService;

/**
 * Class TaxService
 * @package Wizaplace\SDK\Pim\Tax
 */
class TaxService extends AbstractService
{
    /**
     * @return Tax[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function listTaxes(): array
    {
        $this->client->mustBeAuthenticated();
        $taxes = $this->client->get("taxes");

        return array_map(static function ($tax) {
            return new Tax($tax);
        }, $taxes);
    }
}
