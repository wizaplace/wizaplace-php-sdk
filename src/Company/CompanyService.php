<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Company;

use Wizaplace\AbstractService;
use Wizaplace\Authentication\AuthenticationRequired;

class CompanyService extends AbstractService
{
    /**
     * @throws AuthenticationRequired
     */
    public function register(CompanyRegistration $companyRegistration): Company
    {
        $this->client->mustBeAuthenticated();

        $responseData = $this->client->post('companies', [
            'json' => [
                'name' => $companyRegistration->getName(),
                'email' => $companyRegistration->getEmail(),
                'description' => $companyRegistration->getDescription(),
                'slug' => $companyRegistration->getSlug(),
                'address' => $companyRegistration->getAddress(),
                'country' => $companyRegistration->getCountry(),
                'zipcode' => $companyRegistration->getZipcode(),
                'city' => $companyRegistration->getCity(),
                'phoneNumber' => $companyRegistration->getPhoneNumber(),
                'url' => $companyRegistration->getUrl(),
                'fax' => $companyRegistration->getFax(),
                'vatNumber' => $companyRegistration->getVatNumber(),
                'siretNumber' => $companyRegistration->getSiretNumber(),
                'rcs' => $companyRegistration->getRcs(),
                'legalStatus' => $companyRegistration->getLegalStatus(),
                'capital' => $companyRegistration->getCapital(),
            ],
        ]);

        return new Company($responseData);
    }
}
