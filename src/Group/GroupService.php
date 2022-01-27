<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Group;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\Conflict;
use Wizaplace\SDK\Exception\FeatureNotEnabled;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\PaginatedData;

class GroupService extends AbstractService
{
    public function create(string $name): Group
    {
        $this->client->mustBeAuthenticated();
        $endpoint = 'groups';

        try {
            $responseData = $this->client->post(
                $endpoint,
                [
                    RequestOptions::JSON => [
                        'name' => $name
                    ],
                ]
            );

            return new Group($responseData);
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage());
                case 403:
                    throw new AccessDenied($exception->getMessage());
                case 409:
                    throw new Conflict($exception->getMessage());
                case 501:
                    throw new FeatureNotEnabled($exception->getMessage());
                default:
                    throw $exception;
            }
        }
    }

    public function update(Group $group): Group
    {
        $this->client->mustBeAuthenticated();
        $endpoint = 'groups/' . $group->getId();

        try {
            $responseData = $this->client->patch(
                $endpoint,
                [
                    RequestOptions::JSON => [
                        'name' => $group->getName()
                    ],
                ]
            );

            return new Group($responseData);
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage());
                case 403:
                    throw new AccessDenied($exception->getMessage());
                case 409:
                    throw new Conflict($exception->getMessage());
                default:
                    throw $exception;
            }
        }
    }

    public function list(?int $offset = 0, ?int $limit = 10): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        $response = $this->client->get(
            "groups/?offset={$offset}&limit={$limit}"
        );

        return new PaginatedData(
            $response['limit'],
            $response['offset'],
            $response['total'],
            array_map(
                function (array $data): Group {
                    return new Group($data);
                },
                $response['items']
            )
        );
    }

    public function addUsers(string $groupId, array $usersIds): array
    {
        $this->client->mustBeAuthenticated();
        $endpoint = "groups/{$groupId}/users";

        try {
            return $this->client->post(
                $endpoint,
                [
                    RequestOptions::JSON => [
                        'usersIds' => $usersIds
                    ],
                ]
            );
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage());
                case 403:
                    throw new AccessDenied($exception->getMessage());
                case 409:
                    throw new Conflict($exception->getMessage());
                case 501:
                    throw new FeatureNotEnabled($exception->getMessage());
                default:
                    throw $exception;
            }
        }
    }

    public function addUser(string $groupId, int $userId): int
    {
        $this->client->mustBeAuthenticated();
        $endpoint = "groups/{$groupId}/users/{$userId}";

        try {
            return $this->client->post($endpoint)['userId'];
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage());
                case 403:
                    throw new AccessDenied($exception->getMessage());
                case 409:
                    throw new Conflict($exception->getMessage());
                case 501:
                    throw new FeatureNotEnabled($exception->getMessage());
                default:
                    throw $exception;
            }
        }
    }

    public function deleteUser(string $groupId, int $userId): void
    {
        $this->client->mustBeAuthenticated();
        $endpoint = "groups/{$groupId}/users/{$userId}";

        try {
            $this->client->delete($endpoint);
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 400:
                    throw new SomeParametersAreInvalid($exception->getMessage());
                case 403:
                    throw new AccessDenied($exception->getMessage());
                case 409:
                    throw new Conflict($exception->getMessage());
                case 501:
                    throw new FeatureNotEnabled($exception->getMessage());
                default:
                    throw $exception;
            }
        }
    }

    public function deleteUsers(string $groupId): void
    {
        $this->client->mustBeAuthenticated();
        $endpoint = "groups/{$groupId}/users";

        try {
            $this->client->delete($endpoint);
        } catch (ClientException $exception) {
            switch ($exception->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($exception->getMessage());
                case 409:
                    throw new Conflict($exception->getMessage());
                case 501:
                    throw new FeatureNotEnabled($exception->getMessage());
                default:
                    throw $exception;
            }
        }
    }

    public function listUsers(string $groupId, ?int $offset = 0, ?int $limit = 10): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        $response = $this->client->get(
            "groups/{$groupId}/users/?offset={$offset}&limit={$limit}"
        );

        return new PaginatedData(
            $response['limit'],
            $response['offset'],
            $response['total'],
            $response['items']
        );
    }
}
