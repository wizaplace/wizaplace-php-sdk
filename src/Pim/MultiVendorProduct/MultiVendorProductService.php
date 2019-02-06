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
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\File\File;
use Wizaplace\SDK\File\Multipart;
use function theodorejb\polycast\to_string;

/**
 * Class MultiVendorProductService
 * @package Wizaplace\SDK\Pim\MultiVendorProduct
 */
final class MultiVendorProductService extends AbstractService
{
    /**
     * @param string $id
     *
     * @return MultiVendorProduct
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
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

    /**
     * @param MultiVendorProduct $mvp
     *
     * @return string
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function createMultiVendorProduct(MultiVendorProduct $mvp): string
    {
        $this->client->mustBeAuthenticated();
        $mvp->validate(MultiVendorProduct::CONTEXT_CREATE);

        $response = $this->client->post("pim/multi-vendor-products", [
            RequestOptions::JSON => $mvp->toArray(),
        ]);

        return to_string($response['id']);
    }

    /**
     * @param MultiVendorProduct $mvp
     *
     * @return string
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
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

    /**
     * @param string $mvpId
     * @param array  $files
     *
     * @return MultiVendorProduct
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addImageToMultiVendorProduct(string $mvpId, array $files) : MultiVendorProduct
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post("pim/multi-vendor-products/{$mvpId}/images", [
                RequestOptions::MULTIPART => Multipart::createMultipartArray([], $files),
            ]);
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Multi vendor product #${mvpId} not found", $e);
            }
            if ($e->getCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage());
            }
            throw $e;
        }

        return new MultiVendorProduct($response);
    }
}
