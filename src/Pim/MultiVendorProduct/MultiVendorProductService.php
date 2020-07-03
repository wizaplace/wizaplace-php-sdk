<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\MultiVendorProduct;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use http\Client;
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
class MultiVendorProductService extends AbstractService
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

    public function getListMultiVendorProduct(
        ?MultiVendorProductFilter $filter,
        $page = 1,
        $resultsPerPage = 50
    ): MultiVendorProductList {
        $this->client->mustBeAuthenticated();

        $query = [
            'page' => $page,
            'resultsPerPage' => $resultsPerPage,
        ];

        if ($filter instanceof MultiVendorProductFilter) {
            $query = array_merge($query, $filter->toArray());
        }

        $data = $this->client->get(
            "pim/multi-vendor-products",
            [
                RequestOptions::QUERY => $query,
            ]
        );

        $data['page'] = $page;

        return new MultiVendorProductList($data);
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

        $response = $this->client->post(
            "pim/multi-vendor-products",
            [
                RequestOptions::JSON => $mvp->toArray(),
            ]
        );

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

        $response = $this->client->put(
            "pim/multi-vendor-products/$id",
            [
                RequestOptions::JSON => $mvp->toArray(),
            ]
        );

        return to_string($response['id']);
    }

    /**
     * @param string $mvpId
     * @param array $files
     *
     * @param string $alt
     * @return MultiVendorProduct
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addImageToMultiVendorProduct(string $mvpId, array $files, string $alt = ''): MultiVendorProduct
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post(
                "pim/multi-vendor-products/{$mvpId}/images",
                [
                    RequestOptions::MULTIPART => Multipart::createMultipartArray(
                        [
                            'alt' => $alt
                        ],
                        $files),
                ]
            );

            return new MultiVendorProduct($response);
        } catch (ClientException $e) {
            $this->clientException($e, $mvpId);
        }
    }

    /**
     * @param string $mvpId
     * @param string $file
     *
     * @return MultiVendorProductVideo
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addHostedVideoToMultiVendorProduct(string $mvpId, string $file): MultiVendorProductVideo
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post(
                "pim/multi-vendor-products/{$mvpId}/video",
                [
                    RequestOptions::FORM_PARAMS => [
                        'file' => $file,
                    ],
                ]
            );

            return new MultiVendorProductVideo($response);
        } catch (ClientException $e) {
            $this->clientException($e, $mvpId);
        }
    }

    /**
     * @param string $mvpId
     * @param MultiVendorProductFile $file
     *
     * @return MultiVendorProductVideo
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addUploadedVideoToMultiVendorProduct(string $mvpId, MultiVendorProductFile $file): MultiVendorProductVideo
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post(
                "pim/multi-vendor-products/{$mvpId}/video",
                [
                    RequestOptions::MULTIPART => Multipart::createMultipartArray([], [$file]),
                ]
            );

            return new MultiVendorProductVideo($response);
        } catch (ClientException $e) {
            $this->clientException($e, $mvpId);
        }
    }

    /**
     * @param string $mvpId
     *
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function deleteVideoToMultiVendorProduct(string $mvpId): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->delete("pim/multi-vendor-products/{$mvpId}/video");
        } catch (ClientException $e) {
            $this->clientException($e, $mvpId);
        }
    }

    /**
     * @param ClientException $exception
     *
     * @param string $mvpId
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     */
    private function clientException(ClientException $exception, string $mvpId)
    {
        if ($exception->getCode() === 404) {
            throw new NotFound("Multi vendor product #${mvpId} not found", $exception);
        }
        if ($exception->getCode() === 400) {
            throw new SomeParametersAreInvalid($exception->getMessage());
        }
        throw $exception;
    }
}
