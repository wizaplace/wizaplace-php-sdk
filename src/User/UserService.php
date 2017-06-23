<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\User;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;

class UserService extends AbstractService
{
    /**
     * Je ne me base pas sur l'id de l'api key parce qu'un admin pourrait
     * consulter le profile de quelqu'un d'autre.
     */
    public function getProfileFromId(int $id): User
    {
        try {
            $user = new User($this->client->get("users/{$id}", []));
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new NotFound("User profile #{$id} not found", $e);
            }
            throw $e;
        }

        return $user;
    }

    public function updateUser(User $user)
    {
        $this->client->put(
            'users/'.$user->getId(),
            [
                'form_params' => [
                    'email' => $user->getEmail(),
                    'firstName' => $user->getFirstname(),
                    'lastName' => $user->getLastname(),
                ],
            ]
        );
    }

    public function updateUserAdresses(User $user)
    {
        $this->client->put(
            'users/'.$user->getId().'/addresses',
            [
                'form_params' => [
                    'billing' => $user->getBillingAddress(),
                    'shipping' => $user->getShippingAddress(),
                ],
            ]
        );
    }

    public function register(
        string $email,
        string $password,
        string $firstName = '',
        string $lastName = ''
    ): int {

        try {
            $userData = $this->client->post(
                'users',
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
        $this->client->post(
            'users/password/recover',
            [
                'form_params' => [
                    'email' => $email,
                ],
            ]
        );
    }
}
