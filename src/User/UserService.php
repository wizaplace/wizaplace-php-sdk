<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\User;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;

final class UserService extends AbstractService
{
    private const BIRTHDAY_FORMAT = 'Y-m-d';

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
     * @throws SomeParametersAreInvalid
     */
    public function updateUser(UpdateUserCommand $command)
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $this->client->put(
            "users/{$command->getUserId()}",
            [
                'form_params' => [
                    'email' => $command->getEmail(),
                    'title' => is_null($command->getTitle()) ? null : $command->getTitle()->getValue(),
                    'firstName' => $command->getFirstName(),
                    'lastName' => $command->getLastName(),
                    'birthday' => is_null($command->getBirthday()) ? null : $command->getBirthday()->format(self::BIRTHDAY_FORMAT),
                ],
            ]
        );
    }

    /**
     * Update the user's addresses.
     *
     * @throws AuthenticationRequired
     * @throws SomeParametersAreInvalid
     */
    public function updateUserAdresses(UpdateUserAddressesCommand $command)
    {
        $this->client->mustBeAuthenticated();
        $command->validate();
        $this->client->put(
            "users/{$command->getUserId()}/addresses",
            [
                'form_params' => [
                    'billing' => self::serializeUserAddressUpdate($command->getBillingAddress()),
                    'shipping' => self::serializeUserAddressUpdate($command->getShippingAddress()),
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

    public function recoverPassword(string $email, ?UriInterface $recoverBaseUrl = null)
    {
        $data = [
            'email' => $email,
        ];
        if (!empty($recoverBaseUrl)) {
            $data['recoverBaseUrl'] = (string) $recoverBaseUrl;
        }

        // On attend une 204 donc pas de retour
        $this->client->post(
            'users/password/recover',
            [
                RequestOptions::FORM_PARAMS => $data,
            ]
        );
    }

    public function changePasswordWithRecoveryToken(string $token, string $newPassword): void
    {
        $this->client->put("users/password/change-with-token", [
            RequestOptions::JSON => [
                'token' => $token,
                'password' => $newPassword,
            ],
        ]);
    }

    /**
     * @param int $userId
     * @param string $newPassword
     * @throws AuthenticationRequired
     */
    public function changePassword(int $userId, string $newPassword): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->put("users/$userId/password", [
            RequestOptions::JSON => [
                'password' => $newPassword,
            ],
        ]);
    }

    private static function serializeUserAddressUpdate(UpdateUserAddressCommand $command): array
    {
        return array_filter([
            'title' => is_null($command->getTitle()) ? null : $command->getTitle()->getValue(),
            'firstname' => $command->getFirstName(),
            'lastname' => $command->getLastName(),
            'company' => $command->getCompany(),
            'phone' => $command->getPhone(),
            'address' => $command->getAddress(),
            'address_2' => $command->getAddressSecondLine(),
            'zipcode' => $command->getZipCode(),
            'city' => $command->getCity(),
            'country' => $command->getCountry(),
        ]);
    }
}
