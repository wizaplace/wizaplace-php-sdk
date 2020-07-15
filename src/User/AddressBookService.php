<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizacha\Bridge\Symfony\Response\AccessDeniedJsonResponse;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\PaginatedData;

class AddressBookService extends AbstractService
{
    /**
     * @param int $userId
     * @param array $data
     * @return string
     * @throws BadCredentials
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function createAddressInAddressBook(int $userId, array $data): string
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                "users/{$userId}/address-book/addresses",
                [
                    RequestOptions::JSON => $data
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage(), 400);
            }
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BadCredentials($e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound('User not found', $e);
            }
            throw $e;
        }
    }

    /**
     * @param int $userId
     * @param string $addressId
     * @param UpdateAddressBookCommand $command
     *
     * @return mixed|null
     * @throws AccessDeniedJsonResponse
     */
    public function replaceAddressInAddressBook(int $userId, string $addressId, UpdateAddressBookCommand $command)
    {
        $this->client->mustBeAuthenticated();

        return $this->client->put(
            "users/{$userId}/address-book/addresses/{$addressId}",
            [
                RequestOptions::JSON => $this->filterPayload(
                    [
                        'label' => $command->getLabel(),
                        'title' => \is_null($command->getTitle()) ? null : $command->getTitle()->getValue(),
                        'firstname' => $command->getFirstName(),
                        'lastname' => $command->getLastName(),
                        'company' => $command->getCompany(),
                        'phone' => $command->getPhone(),
                        'address' => $command->getAddress(),
                        'address_2' => $command->getAddressSecondLine(),
                        'zipcode' => $command->getZipCode(),
                        'city' => $command->getCity(),
                        'country' => $command->getCountry(),
                        'division_code' => $command->getAddressSecondLine(),
                        'comment' => $command->getComment(),
                        'fromUserAddress' => $command->getFromUserAddress(),
                    ]
                ),
            ]
        );
    }

    /**
     * Remove null values
     *
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    private function filterPayload(array $data): array
    {
        return array_filter(
            $data,
            /** @param mixed $d */
            function ($d): bool {
                return false === \is_null($d);
            }
        );
    }

    /**
     * @param int $userId
     * @param string $addressId
     *
     * @throws AccessDeniedJsonResponse
     * @throws NotFound
     */
    public function removeAddressBook(int $userId, string $addressId)
    {
        $this->client->mustBeAuthenticated();

        $this->client->delete("users/{$userId}/address-book/addresses/{$addressId}");
    }

    /**
     * @param int $idUser
     * @param int|null $offset
     * @param int|null $limit
     * @return PaginatedData
     * @throws AccessDenied
     * @throws \GuzzleHttp\Exception\ClientException
     */
    public function listAddressBook(int $idUser, ?int $offset = 0, ?int $limit = 20): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        $response = $this->client->get(
            "users/{$idUser}/address-book/addresses?offset={$offset}&limit={$limit}"
        );

        return new PaginatedData(
            $response['limit'],
            $response['offset'],
            $response['total'],
            array_map(
                function (array $addressBook): AddressBook {
                    return new AddressBook($addressBook);
                },
                $response['items']
            )
        );
    }

    /**
     * @param int $userId
     * @param AddressType $addressType
     * @return string
     * @throws BadCredentials
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function copyAddressInAddressBook(int $userId, AddressType $addressType): string
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                "users/{$userId}/address-book/addresses",
                [
                    RequestOptions::JSON => [
                        'fromUserAddress' => $addressType->getValue(),
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage(), 400);
            }
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BadCredentials($e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound('User not found', $e);
            }
            throw $e;
        }
    }
}
