<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\MultiVendorProduct;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\NotFound;
use function theodorejb\polycast\to_int;
use function theodorejb\polycast\to_string;

final class MultiVendorProductService extends AbstractService
{
    public function getMultiVendorProductById(string $id): MultiVendorProduct
    {
        $this->client->mustBeAuthenticated();
        try {
            $response = $this->client->get("pim/multi-vendor-products/$id");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Multi vendor product #${id} not found", $e);
            }
            throw $e;
        }

        return new MultiVendorProduct($response);
    }

    public function createMultiVendorProduct(MultiVendorProduct $mvp): string
    {
        $this->client->mustBeAuthenticated();
        $mvp->validate(MultiVendorProduct::CONTEXT_CREATE);

        $response = $this->client->post("pim/multi-vendor-products", [
            RequestOptions::JSON => $mvp->toArray(),
        ]);

        return to_string($response['id']);
    }

    public function updateMultiVendorProduct(MultiVendorProduct $mvp): string
    {
        $this->client->mustBeAuthenticated();
        $mvp->validate(MultiVendorProduct::CONTEXT_UPDATE);

        $id = $mvp->getId();

        $response = $this->client->put("pim/multi-vendor-products/$id", [
            RequestOptions::JSON => $mvp->toArray(),
        ]);

        return to_string($response['id']);
    }
}
