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
     * @return mixed|null
     * @throws \Exception
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

        $files = $organisation->getFiles();

        $dataToSend = [];

        $flatArray = $this->flattenArray($data);

        foreach ($flatArray as $key => $value) {
            $dataToSend[] = [
                'name'  => $key,
                'contents' => $value,
            ];
        }

        foreach ($files as $file) {
            /** @var OrganisationFile $file */
            $dataToSend[] = [
                'name' => $file->getName(),
                'contents' => $file->getContents(),
            ];
        }

        try {
            $registrationReturn = $this->client->post('organisations/registrations', [
                RequestOptions::MULTIPART => $dataToSend,
            ]);

            return $registrationReturn;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new \Exception("Invalid request", $e);
            }
            throw $e;
        }
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
            $organisationData = $this->client->get('organisations/'.$organisationId);

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
            $listUsers = $this->client->get('organisations/'.$organisationId.'/users');

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
     *
     * @param string $organisationId
     * @param Organisation $organisation
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
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
            $this->client->put('organisations/'.$organisationId, [
                RequestOptions::FORM_PARAMS => $data,
            ]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new \Exception("Invalid request", $e);
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
     * @param string $organisationId
     * @param OrganisationAddress $address
     * @param OrganisationAddress $shippingAddress
     * @return mixed|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     */
    public function updateOrganisationAddresses(string $organisationId, OrganisationAddress $address, OrganisationAddress $shippingAddress)
    {
        $this->client->mustBeAuthenticated();

        $data = [];

        $data['address'] = $address->toArray();
        $data['shippingAddress'] = $shippingAddress->toArray();

        try {
            $responseData = $this->client->put('organisations/'.$organisationId.'/addresses', [
                RequestOptions::JSON => $data,
            ]);

            return $responseData;
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new \Exception("Invalid request", $e);
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
     * @return array|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     */
    public function addBasket(string $organisationId, string $name)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post('organisations/'.$organisationId.'/baskets', [
                RequestOptions::FORM_PARAMS => [
                    'name' => $name,
                ],
            ]);

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
     * @return array|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     */
    public function lockBasket(string $organisationId, string $basketId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post('organisations/'.$organisationId.'/baskets/'.$basketId.'/lock');

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
     * @return array|null
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws UserDoesntBelongToOrganisation
     */
    public function validateBasket(string $organisationId, string $basketId)
    {
        $this->client->mustBeAuthenticated();

        try {
            $responseData = $this->client->post('organisations/'.$organisationId.'/baskets/'.$basketId.'/validation');

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
            } elseif ($value instanceof OrganisationItemInterface) {
                $output = array_merge($output, $this->flattenArray($value->toArray(), $newKey));
            } else {
                $output[$newKey] = $value;
            }
        }

        return $output;
    }
}
