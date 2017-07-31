<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Company;

use Psr\Http\Message\StreamInterface;
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

    /**
     * @param string[]|resource[]|StreamInterface[] $files
     * @return FiledUploadResult[]
     */
    public function uploadRegistrationFiles(int $companyId, array $files): array
    {
        $this->client->mustBeAuthenticated();

        $parts = [];
        foreach ($files as $name => $file) {
            if (is_string($file)) {
                $filename = $file;
                $contents = file_get_contents($file);
            } elseif (is_resource($file)) {
                $filename = stream_get_meta_data($file)["uri"] ?? null;
                $contents = $file;
            } elseif ($file instanceof StreamInterface) {
                $filename = $file->getMetadata('uri');
                $contents = $file;
            } else {
                throw new \TypeError('$file must be a string, a resource, or a StreamInterface');
            }

            $parts[] = [
                'name' => $name,
                'contents' => $contents,
                'filename' => $filename,
            ];
        }

        $responseData = $this->client->post("companies/$companyId/files", [
            'multipart' => $parts,
        ]);

        return array_map(function (array $data) {
            return new FiledUploadResult($data['error'] ?? null);
        }, $responseData);
    }
}
