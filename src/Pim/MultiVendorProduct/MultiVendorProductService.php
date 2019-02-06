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

    /**
     * @param string $mvpId
     * @param array $files
     * @return MultiVendorProduct
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
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

    /**
     * @param string $mvpId
     * @param string $file
     * @return MultiVendorProductVideo
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function addHostedVideoToMultiVendorProduct(string $mvpId, string $file): MultiVendorProductVideo
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post("pim/multi-vendor-products/{$mvpId}/video", [
                RequestOptions::FORM_PARAMS => [
                    'file' => $file,
                ],
            ]);

            return new MultiVendorProductVideo($response);
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Multi vendor product #${mvpId} not found", $e);
            }
            if ($e->getCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * @param string $mvpId
     * @param MultiVendorProductFile $file
     * @return MultiVendorProductVideo
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function addUploadedVideoToMultiVendorProduct(string $mvpId, MultiVendorProductFile $file): MultiVendorProductVideo
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post("pim/multi-vendor-products/{$mvpId}/video", [
                RequestOptions::MULTIPART => Multipart::createMultipartArray([], [$file]),
            ]);

            return new MultiVendorProductVideo($response);
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Multi vendor product #${mvpId} not found", $e);
            }
            if ($e->getCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * @param string $mvpId
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function deleteVideoToMultiVendorProduct(string $mvpId): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->delete("pim/multi-vendor-products/{$mvpId}/video");
        } catch (ClientException $e) {
            if ($e->getCode() === 404) {
                throw new NotFound("Multi vendor product #${mvpId} not found", $e);
            }
            if ($e->getCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage());
            }
            throw $e;
        }
    }
}
