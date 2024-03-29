<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Organisation;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Order\Order;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\Exception\UserDoesntBelongToOrganisation;
use Wizaplace\SDK\File\File;
use Wizaplace\SDK\File\Multipart;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\PaginationHttpHeaders;
use Wizaplace\SDK\User\User;
use Wizaplace\SDK\Vendor\Order\OrderSummary;

/**
 * Class OrganisationService
 * @package Wizaplace\SDK\Organisation
 */
class OrganisationService extends AbstractService
{
    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1registrations/post
     *
     * @param Organisation $organisation
     *
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function register(Organisation $organisation)
    {
        $data = [
            'name' => $organisation->getName(),
            'businessName' => $organisation->getLegalInformationBusinessName(),
            'businessUnitName' => $organisation->getBusinessUnitName(),
            'businessUnitCode' => $organisation->getBusinessUnitCode(),
            'siret' => $organisation->getLegalInformationSiret(),
            'vatNumber' => $organisation->getLegalInformationVatNumber(),
            'address' => $organisation->getAddress(),
            'shippingAddress' => $organisation->getShippingAddress(),
            'administrator' => $organisation->getAdministrator(),
        ];

        try {
            $registrationReturn = $this->client->post(
                'organisations/registrations',
                [
                    RequestOptions::MULTIPART => Multipart::createMultipartArray($data, $organisation->getFiles()),
                ]
            );

            return $registrationReturn;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new \Exception("Invalid request", $e->getResponse()->getStatusCode(), $e);
            }
            throw $e;
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}/get
     *
     * @param string $organisationId
     *
     * @return Organisation
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function get(string $organisationId): Organisation
    {
        $this->client->mustBeAuthenticated();

        try {
            $organisationData = $this->client->get('organisations/' . $organisationId);

            return new Organisation($organisationData);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * Return an array of all organisations, with the count & total
     *
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations/get
     *
     * @return array
     * @throws AuthenticationRequired
     * @throws BadCredentials
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getList(): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $listOrganisations = $this->client->get('organisations');

            return $listOrganisations;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BadCredentials($e);
            }
            throw $e;
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1users/get
     *
     * @param string $organisationId
     *
     * @return array
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getListUsers(string $organisationId): array
    {
        $this->client->mustBeAuthenticated();

        try {
            $listUsers = $this->client->get('organisations/' . $organisationId . '/users');

            return $listUsers;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * Allow to add a new user to the organisation
     *
     * @param string $organisationId
     * @param array  $data
     * @param array  $files
     *
     * @return User
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addNewUser(string $organisationId, array $data, array $files): User
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->post(
                "organisations/{$organisationId}/users",
                [
                    RequestOptions::MULTIPART => Multipart::createMultipartArray($data, $files),
                ]
            );

            return new User($response);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new \Exception($e->getMessage(), 400, $e);
            }
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}/put
     *
     * @param string       $organisationId
     * @param Organisation $organisation
     *
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function updateOrganisation(string $organisationId, Organisation $organisation)
    {
        $this->client->mustBeAuthenticated();

        $data = [
            'name' => $organisation->getName(),
            'businessName' => $organisation->getLegalInformationBusinessName(),
            'businessUnitName' => $organisation->getBusinessUnitName(),
            'businessUnitCode' => $organisation->getBusinessUnitCode(),
            'siret' => $organisation->getLegalInformationSiret(),
            'vatNumber' => $organisation->getLegalInformationVatNumber(),
        ];

        try {
            $this->client->put(
                'organisations/' . $organisationId,
                [
                    RequestOptions::FORM_PARAMS => $data,
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new \Exception("Invalid request", $e->getResponse()->getStatusCode(), $e);
            }
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1addresses/put
     *
     * @param string              $organisationId
     * @param OrganisationAddress $address
     * @param OrganisationAddress $shippingAddress
     *
     * @return mixed|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function updateOrganisationAddresses(string $organisationId, OrganisationAddress $address, OrganisationAddress $shippingAddress)
    {
        $this->client->mustBeAuthenticated();

        $data = [];

        $data['address'] = $address->toArray();
        $data['shippingAddress'] = $shippingAddress->toArray();

        try {
            $responseData = $this->client->put(
                'organisations/' . $organisationId . '/addresses',
                [
                    RequestOptions::JSON => $data,
                ]
            );

            return $responseData;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new \Exception("Invalid request", $e->getResponse()->getStatusCode(), $e);
            }
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1baskets/post
     *
     * @param string $organisationId
     * @param string $name
     *
     * @return array|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addBasket(string $organisationId, string $name)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post(
                'organisations/' . $organisationId . '/baskets',
                [
                    RequestOptions::FORM_PARAMS => [
                        'name' => $name,
                    ],
                ]
            );

            return $responseData;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1baskets~1{basketId}~1lock/post
     *
     * @param string $organisationId
     * @param string $basketId
     *
     * @return array|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function lockBasket(string $organisationId, string $basketId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post('organisations/' . $organisationId . '/baskets/' . $basketId . '/lock');

            return $responseData;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1baskets~1{basketId}~1validation/post
     *
     * @param string $organisationId
     * @param string $basketId
     *
     * @return array|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function validateBasket(string $organisationId, string $basketId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post('organisations/' . $organisationId . '/baskets/' . $basketId . '/validation');

            return $responseData;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * @param string $organisationId
     * @param string $basketId
     * @param int    $paymentId
     * @param bool   $acceptTerms
     * @param string $redirectUrl
     *
     * @return mixed|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function checkoutBasket(string $organisationId, string $basketId, int $paymentId, bool $acceptTerms, string $redirectUrl)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post(
                'organisations/' . $organisationId . '/baskets/' . $basketId . '/order',
                [
                    RequestOptions::FORM_PARAMS => [
                        'paymentId' => $paymentId,
                        'acceptTermsAndConditions' => $acceptTerms,
                        'redirectUrl' => $redirectUrl,
                    ],
                ]
            );

            return $responseData;
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage(), $e->getCode(), $e);
                case 403:
                    throw new UserDoesntBelongToOrganisation("You don't belong to this organisation", $e);
                case 404:
                    throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * Hide a basket, set hide flag to true
     *
     * @param string $organisationId
     * @param string $basketId
     *
     * @return mixed|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function hideBasket(string $organisationId, string $basketId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post('organisations/' . $organisationId . '/baskets/' . $basketId . '/hide');

            return $responseData;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * Allow to get the organisation's information from a user
     *
     * @param int $userId
     *
     * @return Organisation
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getOrganisationFromUserId(int $userId): Organisation
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->get("users/{$userId}/organisation");

            return new Organisation($responseData);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("You don't belong to an organisation", $e);
            }

            throw $e;
        }
    }

    /**
     * Allow to list the organisation's user groups
     *
     * @param string $organisationId
     *
     * @return \Iterator|OrganisationGroup[]
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getOrganisationGroups(string $organisationId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get("organisations/{$organisationId}/groups");

            $data = new \ArrayIterator();
            foreach ($response['_embedded']['groups'] as $groupData) {
                $data->append(new OrganisationGroup($groupData));
            }

            return $data;
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    throw new \Exception("You don't belong to the organisation.", Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("The organisation doesn't exist.", $e);

                default:
                    throw $e;
            }
        }
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1groups/post
     *
     * @param string $organisationId
     * @param string $name
     * @param string $type
     *
     * @return array|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function createGroup(string $organisationId, string $name, string $type)
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->post(
                "organisations/{$organisationId}/groups",
                [
                    RequestOptions::FORM_PARAMS => [
                        "name" => $name,
                        "type" => $type,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new UserDoesntBelongToOrganisation("You don't belong to the administrator group", $e);
            }
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("The organisation doesn't exist", $e);
            }
            throw $e;
        }
    }

    /**
     * Allow to add a new user to the group.
     *
     * @param string $groupId
     * @param int    $userId
     *
     * @return void
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function addUserToGroup(string $groupId, int $userId): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->post(
                "organisations/groups/{$groupId}/users",
                [
                    RequestOptions::FORM_PARAMS => [
                        'userId' => $userId,
                    ],
                ]
            );
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_BAD_REQUEST:
                    throw new \Exception("Invalid request", Response::HTTP_BAD_REQUEST, $e);

                case Response::HTTP_FORBIDDEN:
                    throw new \Exception("You don't belong to the admin user group of the organisation", Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("The user group doesn't exist", $e);

                default:
                    throw $e;
            }
        }
    }

    /**
     * Allow to get the list of group's users
     *
     * @param string $groupId
     *
     * @return \Iterator|User[]
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getGroupUsers(string $groupId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get("organisations/groups/{$groupId}/users");

            $users = new \ArrayIterator();
            foreach ($response['_embedded']['users'] as $user) {
                $users->append(new User($user));
            }

            return $users;
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    throw new \Exception("You don't belong to the admin user group of the organisation", Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("The user group doesn't exist", $e);

                default:
                    throw $e;
            }
        }
    }

    /**
     * Allow to remove a user from the group.
     *
     * @param string $groupId
     * @param int    $userId
     *
     * @return void
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function removeUserFromGroup(string $groupId, int $userId): void
    {
        $this->client->mustBeAuthenticated();

        try {
            $this->client->delete("organisations/groups/{$groupId}/users/{$userId}");
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_BAD_REQUEST:
                    throw new \Exception("Invalid request", Response::HTTP_BAD_REQUEST, $e);

                case Response::HTTP_FORBIDDEN:
                    throw new \Exception("You don't belong to the admin user group of the organisation", Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("The user group doesn't exist", $e);

                default:
                    throw $e;
            }
        }
    }

    /**
     * Allow to list the organisation's baskets
     *
     * @param string $organisationId
     *
     * @return \Iterator|OrganisationBasket[]
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getOrganisationBaskets(string $organisationId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get("organisations/{$organisationId}/baskets");

            $data = new \ArrayIterator();
            foreach ($response['_embedded']['baskets'] as $basketData) {
                $data->append(new OrganisationBasket($basketData));
            }

            return $data;
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    throw new \Exception("You don't belong to the organisation.", Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("The organisation doesn't exist.", $e);

                default:
                    throw $e;
            }
        }
    }

    /**
     * Allow to list the organisation's orders
     *
     * @deprecated Using getOrganisationOrders() is deprecated, use getOrganisationPaginatedOrders() instead.
     *
     * @param string $organisationId
     *
     * @param int    $start Offset
     * @param int    $limit The length (min 1; max 10)
     *
     * @return OrganisationOrder[]
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getOrganisationOrders(string $organisationId, int $start = 0, int $limit = 10)
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get(
                "organisations/{$organisationId}/orders",
                [
                    RequestOptions::QUERY => [
                        'start' => $start,
                        'limit' => $limit,
                    ],
                ]
            );

            $data['total'] = $response['total'];
            $data['count'] = $response['count'];
            $data['orders'] = [];

            foreach ($response['_embedded']['orders'] as $orderData) {
                $data['orders'][] = new OrderSummary($orderData);
            }

            return $data;
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $response = json_decode($e->getResponse());
                    throw new \Exception($response->message, Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("The organisation doesn't exist.", $e);

                default:
                    throw $e;
            }
        }
    }

    /**
     * Allow to list the organisation's paginated orders
     *
     * @param string $organisationId
     *
     * @param int|null $start Offset
     * @param int|null $limit The length (min 1; max 10)
     *
     * @return PaginatedData
     *
     * @throws AccessDenied
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getOrganisationPaginatedOrders(string $organisationId, int $start = null, int $limit = null): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        try {
            [$result, $paginationHeaders] = $this->client->getWithPaginationHeaders(
                "organisations/{$organisationId}/orders",
                [
                    RequestOptions::QUERY => [
                        'start' => $start,
                        'limit' => $limit,
                    ],
                ]
            );

            $data['count'] = $result['count'];
            $data['orders'] = [];

            foreach ($result['_embedded']['orders'] as $orderData) {
                $data['orders'][] = new OrderSummary($orderData);
            }

            return new PaginatedData(
                (int) $paginationHeaders->getLimit(),
                (int) $paginationHeaders->getOffset(),
                (int) $paginationHeaders->getTotal(),
                $data
            );
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    throw new AccessDenied(
                        "You're neither a marketplace administrator nor an organisation's administrator",
                        Response::HTTP_FORBIDDEN,
                        $e
                    );

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("The organisation doesn't exist.", $e);

                default:
                    throw $e;
            }
        }
    }

    /**
     * @param int $orderId
     *
     * @return Order
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     * @throws \Exception
     */
    public function getOrder(int $orderId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $response = $this->client->get("organisations/order/{$orderId}");

            return new Order($response);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case Response::HTTP_FORBIDDEN:
                    $response = json_decode($e->getResponse());
                    throw new \Exception($response->message, Response::HTTP_FORBIDDEN, $e);

                case Response::HTTP_NOT_FOUND:
                    throw new NotFound("This order doesn't exists.", $e);

                default:
                    throw $e;
            }
        }
    }
}
