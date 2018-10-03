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
use Wizaplace\SDK\ArrayableInterface;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
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
                RequestOptions::MULTIPART => $this->createMultipartArray([], $files),
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
     * This method help to have an array compliant to Guzzle for multipart POST/PUT for the organisation process
     * There are exception in the process for OrganisationAddress and OrganisationAdministrator which needs to be transformed to array
     * prior to processing
     *
     * Ex:
     * ['name' => 'obiwan', ['address' => ['street' => 'main street', 'city' => 'Mos Esley']]
     * needs to be flatten to
     * ['name' => 'obiwan', 'address[street]' => 'main street', 'address[city]' => 'Mos esley']
     *
     * @param array $array
     * @param string $originalKey
     * @return array
     */
    private function flattenArray(array $array, string $originalKey = '')
    {
        $output = [];

        foreach ($array as $key => $value) {
            $newKey = $originalKey;
            if (empty($originalKey)) {
                $newKey .= $key;
            } else {
                $newKey .= '['.$key.']';
            }

            if (is_array($value)) {
                $output = array_merge($output, $this->flattenArray($value, $newKey));
            } elseif ($value instanceof ArrayableInterface) {
                $output = array_merge($output, $this->flattenArray($value->toArray(), $newKey));
            } else {
                $output[$newKey] = $value;
            }
        }

        return $output;
    }

    /**
     * @param array                    $data
     * @param MultiVendorProductFile[] $files
     *
     * @return array
     */
    private function createMultipartArray(array $data, array $files) : array
    {
        $dataToSend = [];

        $flatArray = $this->flattenArray($data);

        foreach ($flatArray as $key => $value) {
            $dataToSend[] = [
                'name'  => $key,
                'contents' => $value,
            ];
        }

        foreach ($files as $file) {
            $dataToSend[] = [
                'name' => $file->getName(),
                'contents' => $file->getContents(),
            ];
        }

        return $dataToSend;
    }
}
