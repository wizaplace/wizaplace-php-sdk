<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Organisation;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use http\Exception\InvalidArgumentException;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\UserDoesntBelongToOrganisation;

class OrganisationService extends AbstractService
{

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1registrations/post
     *
     * @param Organisation $organisation
     * @throws AuthenticationRequired
     */
    public function register(Organisation $organisation)
    {
        $this->client->mustBeAuthenticated();

        $registrationReturn = $this->client->post('organisations/registrations', [
            RequestOptions::FORM_PARAMS => [
                'name' => $organisation->getName(),
                'businessName' => $organisation->getLegalInformationBusinessName(),
                'businessUnitName' => $organisation->getBusinessUnitName(),
                'businessUnitCode' => $organisation->getBusinessUnitCode(),
                'siret' => $organisation->getLegalInformationSiret(),
                'vatNumber' => $organisation->getLegalInformationVatNumber(),
                'address' => $organisation->getAddress(),
                'shippingAddress' => $organisation->getShippingAddress(),
                'administrator' => $organisation->getAdministrator(),
                'identityCard',//fichier
                'proofOfAppointment',//fichier
            ]
        ]);
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}/get
     *
     * @param string $organisationId
     * @return Organisation
     * @throws AuthenticationRequired
     * @throws UserDoesntBelongToOrganisation
     * @throws NotFound
     */
    public function get(string $organisationId) : Organisation
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
     */
    public function getList() : array
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
     * @return array
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     */
    public function getListUsers(string $organisationId) : array
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
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}/put
     */
    public function organisationUpdate()
    {
        $this->client->mustBeAuthenticated();
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1addresses/put
     *
     * @param string $organisationId
     * @param OrganisationAddress $address
     * @param OrganisationAddress $shippingAddress
     * @return mixed|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     */
    public function organisationAddressesUpdate(string $organisationId, OrganisationAddress $address, OrganisationAddress $shippingAddress)
    {
        $this->client->mustBeAuthenticated();

        $data = [];

        $data['address'] = $address->toArray();
        $data['shippingAddress'] = $shippingAddress->toArray();

        try {
            $responseData = $this->client->put('organisations/' . $organisationId . '/addresses', [
                RequestOptions::JSON => $data
            ]);

            return $responseData;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new InvalidArgumentException("Invalid request", $e);
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
     */
    public function addBasket()
    {
        $this->client->mustBeAuthenticated();
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1baskets~1{basketId}~1lock/post
     */
    public function lockBasket()
    {
        $this->client->mustBeAuthenticated();
    }

    /**
     * https://sandbox.wizaplace.com/api/v1/doc/#/paths/~1organisations~1{organisationId}~1baskets~1{basketId}~1validation/post
     */
    public function basketValidation()
    {
        $this->client->mustBeAuthenticated();
    }
}
