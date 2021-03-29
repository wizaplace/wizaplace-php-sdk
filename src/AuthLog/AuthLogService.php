<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\AuthLog;

use Wizaplace\SDK\AbstractService;

class AuthLogService extends AbstractService
{
    /**
     * Get AuhtLog
     *
     * int $id: AuthLog specific id
     */
    public function get(int $id): array
    {
        $this->client->mustBeAuthenticated();

        return $this->client->get(
            sprintf('security/viewlog/%d', $id)
        );
    }

    /**
     * Search AuthLogs
     *
     * $options (array): an array of optionals parameters :
     * - limit (int) : records per page
     * - login (string|array)
     * - status (string|array) : ['SUCCESS', 'ACCESS_DENIED', 'UNKNOWN_LOGIN', 'WRONG_PASSWORD', 'PENDING_ACCOUNT', 'DESACTIVATED_ACCOUNT']
     * - destination (string|array) : ['FRONT', 'VENDOR_BACKOFFICE', 'ADMINISTRATOR_BACKOFFICE']
     * - periode (array) : array of date string in RFC3339 format
     *   - from (string)
     *   - to (string)
     * - sort_by (string) : ['id', 'createdAt', 'login', 'status', 'source', 'destination']
     * - sort_order (string) : ['asc', 'desc' ]
     *
     * $page (int) : pagination offset
     */
    public function search(array $options = [], int $page = 0): array
    {
        $this->client->mustBeAuthenticated();

        $query = \http_build_query(
            $this->defaultSearchOptions($options)
        );

        return $this->client->get(
            sprintf(
                'security/viewlogs/%d.json?%s',
                $page,
                $query
            )
        );
    }

    private function defaultSearchOptions(array $options): array
    {
        $default = [
            'start' => 0,
            'limit' => 100,
            'login' => null,
            'status' => null,
            'source' => null,
            'destination' => null,
            'period' => [],
            'sort_by' => 'id',
            'sort_order' => 'desc',
        ];

        return array_merge($default, $options);
    }
}
