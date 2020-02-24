<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Attribute;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Pim\Attribute\Attribute;

class AttributeService extends AbstractService
{
    public function getAttribute(int $featureId): Attribute
    {
        $this->client->mustBeAuthenticated();

        try {
            return new Attribute($this->client->get("features/$featureId"));
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new NotFound("Attribute not found.");
            }

            throw $exception;
        }
    }
}
