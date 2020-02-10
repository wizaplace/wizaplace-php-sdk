<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);


namespace Wizaplace\SDK\Pim\Moderation;

use Wizaplace\SDK\AbstractService;

/**
 * Class ModerationService
 * @package Wizaplace\SDK\Pim\Moderation
 */
class ModerationService extends AbstractService
{
    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function moderateByCompany(int $companyId, string $moderationAction): void
    {
        $authorizedActions = [
            'approve', 'disapprove', 'standby'
        ];
        if (false === in_array($moderationAction, $authorizedActions)) {
            // Throw new exception
        }

        $this->client->mustBeAuthenticated();
        $this->client->put("moderation/companies/{$companyId}/products/{moderationAction}");
    }
}
