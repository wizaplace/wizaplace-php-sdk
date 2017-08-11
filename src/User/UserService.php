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
use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Exception\NotFound;

final class UserService extends AbstractService
{
    /**
     * Je ne me base pas sur l'id de l'api key parce qu'un admin pourrait
     * consulter le profile de quelqu'un d'autre.
     * @throws AuthenticationRequired
     * @throws NotFound
     */
    public function getProfileFromId(int $id): User
    {
        $this->client->mustBeAuthenticated();
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

    /**
     * Update the information of a user profile.
     *
     * @throws AuthenticationRequired
     */
    public function updateUser(int $userId, string $email, string $firstName, string $lastName)
    {
        $this->client->mustBeAuthenticated();
        $this->client->put(
            "users/$userId",
            [
                'form_params' => [
                    'email' => $email,
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                ],
            ]
        );
    }

    /**
     * Update the user's addresses.
     *
     * @throws AuthenticationRequired
     */
    public function updateUserAdresses(User $user)
    {
        $this->client->mustBeAuthenticated();
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

    /**
     * Register to create a user account.
     *
     * @return int ID of the created user.
     *
     * @throws UserAlreadyExists The email address is already used by a user account.
     */
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
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 409) {
                throw new UserAlreadyExists($e);
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

    public function changePassword(int $userId, string $newPassword)
    {
        $this->client->mustBeAuthenticated();
        $this->client->put("users/$userId/password", [
            'json' => [
                'password' => $newPassword,
            ],
        ]);
    }
}
