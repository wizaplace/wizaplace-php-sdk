<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Attribute;

use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Pim\Attribute\Attribute;
use Wizaplace\SDK\Traits\AssertRessourceNotFoundTrait;

class AttributeService extends AbstractService
{
    use AssertRessourceNotFoundTrait;

    public function getAttribute(int $featureId): Attribute
    {
        $this->client->mustBeAuthenticated();

        return $this->assertRessourceNotFound(
            function () use ($featureId): Attribute {
                return new Attribute($this->client->get("features/$featureId"));
            },
            "Attribute #{$featureId} not found."
        );
    }
}
