<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\User;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;

class UserService extends AbstractService
{

    /**
     * @throws BadCredentials
     */
    public function authenticate(string $email, string $password): ApiKey
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->baseUrl.'/users/authenticate',
                [
                    'auth' => [$email, $password],
                ]
            );
        } catch (\Exception $e) {
            if ($e->getCode() === 401) {
                throw new BadCredentials();
            }
            throw $e;
        }

        return new ApiKey(json_decode($response->getBody()->getContents(), true));
    }

    /**
     * Je ne me base pas sur l'id de l'api key parce qu'un admin pourrait
     * consulter le profile de quelqu'un d'autre.
     */
    public function getProfileFromId(int $id, ApiKey $apiKey): User
    {
        try {
            $user = new User($this->get("/users/{$id}", [], $apiKey));
        } catch (\Exception $e) {
            if ($e->getCode() === 404) {
                throw new NotFound($e->getMessage());
            }
            throw $e;
        }

        return $user;
    }

    public function updateUser(User $user, ApiKey $apiKey)
    {
        $this->put(
            '/users/'.$user->getId(),
            [
                'form_params' => [
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstname(),
                    'lastName' => $user->getLastname(),
                ],
            ],
            $apiKey
        );
    }

    public function updateUserAdresses(User $user, ApiKey $apiKey)
    {
        $this->put(
            '/users/'.$user->getId().'/addresses',
            [
                'form_params' => [
                    'billing' => $user->getBillingAddress(),
                    'shipping' => $user->getShippingAddress(),
                ],
            ],
            $apiKey
        );
    }

    public function register(
        string $email,
        string $password,
        string $firstName = '',
        string $lastName = ''
    ): int {

        try {
            $userData = $this->post(
                '/users',
                [
                    'form_params' => [
                        'email' => $email,
                        'password' => $password,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                    ],
                ]
            );
        } catch (\Exception $e) {
            if ($e->getCode() === 409) {
                throw new UserAlreadyExists();
            }
            throw $e;
        }

        return $userData['id'];
    }

    public function recoverPassword(string $email)
    {
        // On attend une 204 donc pas de retour
        $this->post(
            '/users/password/recover',
            [
                'form_params' => [
                    'email' => $email,
                ],
            ]
        );
    }
}
