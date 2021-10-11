<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\User;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\AccessDenied;
use Wizaplace\SDK\Exception\NotFound;
use Wizaplace\SDK\Exception\SomeParametersAreInvalid;
use Wizaplace\SDK\PaginatedData;
use Wizaplace\SDK\Subscription\SubscriptionFilter;
use Wizaplace\SDK\Subscription\SubscriptionSummary;
use Wizaplace\SDK\Traits\AssertRessourceNotFoundTrait;

use function theodorejb\polycast\to_string;

/**
 * Class UserService
 * @package Wizaplace\SDK\User
 */
class UserService extends AbstractService
{
    use AssertRessourceNotFoundTrait;

    private const BIRTHDAY_FORMAT = 'Y-m-d';

    /**
     * @param int $id
     *
     * @return User
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getProfileFromId(int $id): User
    {
        /**
         * We don't rely on the api key because an admin could
         * access the profile of someone else.
         */
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
     * @param UpdateUserCommand $command
     *
     * @throws AuthenticationRequired
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function updateUser(UpdateUserCommand $command)
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        $this->client->put(
            "users/{$command->getUserId()}",
            [
                RequestOptions::JSON => $this->filterPayload(
                    [
                        'email' => $command->getEmail(),
                        'title' => \is_null($command->getTitle()) ? null : $command->getTitle()->getValue(),
                        'firstName' => $command->getFirstName(),
                        'lastName' => $command->getLastName(),
                        'phone' => $command->getPhone(),
                        'lang' => $command->getLanguage(),
                        'birthday' => \is_null($command->getBirthday()) ? null : $command->getBirthday()->format(self::BIRTHDAY_FORMAT),
                        'currencyCode' => $command->getCurrencyCode(),
                        'externalIdentifier' =>  \is_null($command->getExternalIdentifier()) ? null : $command->getExternalIdentifier(),
                        'isProfessional' => \is_null($command->getIsProfessional()) ? null : $command->getIsProfessional(),
                        'intraEuropeanCommunityVAT' => $command->getIntraEuropeanCommunityVAT(),
                        'company' => $command->getCompany(),
                        'jobTitle' => $command->getJobTitle(),
                        'comment' => $command->getComment(),
                        'legalIdentifier' => $command->getLegalIdentifier(),
                        'loyaltyIdentifier' => $command->getLoyaltyIdentifier(),
                        'nationalities' => $command->getCodesA3FromNationalities(),
                        'extra' => $command->getExtra(),
                    ]
                ),
            ]
        );
    }

    public function patchUser(UpdateUserCommand $command): array
    {
        $this->client->mustBeAuthenticated();

        try {
            return $this->client->patch(
                "users/{$command->getUserId()}",
                [
                    RequestOptions::FORM_PARAMS => $this->filterPayload(
                        [
                            'currencyCode' => $command->getCurrencyCode(),
                            'phone' => $command->getPhone(),
                            'externalIdentifier' => $command->getExternalIdentifier(),
                            'isProfessional' => $command->getIsProfessional(),
                            'intraEuropeanCommunityVAT' => $command->getIntraEuropeanCommunityVAT(),
                            'company' => $command->getCompany(),
                            'jobTitle' => $command->getJobTitle(),
                            'comment' => $command->getComment(),
                            'legalIdentifier' => $command->getLegalIdentifier(),
                            'loyaltyIdentifier' => $command->getLoyaltyIdentifier(),
                            'nationalities' => $command->getCodesA3FromNationalities(),
                            'extra' => $command->getExtra(),
                        ]
                    ),
                ]
            );
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied("You must be authenticated as an admin.");
                case 404:
                    throw new NotFound("Currency '{$command->getCurrencyCode()}' not found.");
                case 400:
                    throw new SomeParametersAreInvalid($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    /**
     * Update the user's addresses.
     *
     * @param UpdateUserAddressesCommand $command
     * @throws AuthenticationRequired
     * @throws NotFound
     * @throws SomeParametersAreInvalid
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function updateUserAdresses(UpdateUserAddressesCommand $command)
    {
        $this->client->mustBeAuthenticated();
        $command->validate();

        try {
            $this->client->put(
                "users/{$command->getUserId()}/addresses",
                [
                    RequestOptions::JSON => [
                        'billing' => self::serializeUserAddressUpdate($command->getBillingAddress()),
                        'shipping' => self::serializeUserAddressUpdate($command->getShippingAddress()),
                    ],
                ]
            );
        } catch (ClientException $ex) {
            switch ($ex->getResponse()->getStatusCode()) {
                case 404:
                    throw new NotFound($ex->getMessage(), $ex);
                case 400:
                    throw new SomeParametersAreInvalid($ex->getMessage(), 400);
                default:
                    throw $ex;
            }
        }
    }

    /**
     * Register to create a user account.
     *
     * @param string $email
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @param string|null $lang
     * @param UserAddress|null $billing
     * @param UserAddress|null $shipping
     * @param Nationality[]|null $nationalities
     * @return int ID of the created user.
     *
     * @throws UserAlreadyExists The email address is already used by a user account.
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function register(
        string $email,
        string $password,
        string $firstName = '',
        string $lastName = '',
        UserAddress $billing = null,
        UserAddress $shipping = null,
        array $nationalities = null,
        ?string $lang = null
    ): int {
        try {
            $data = [
                'email'     => $email,
                'password'  => $password,
                'firstName' => $firstName,
                'lastName'  => $lastName,
            ];

            if (\is_null($lang) === false) {
                $data['lang'] = $lang;
            }

            if ($billing instanceof UserAddress && $shipping instanceof UserAddress) {
                $data['billing']  = $billing->toArray();
                $data['shipping'] = $shipping->toArray();
            } elseif ($billing instanceof UserAddress xor $shipping instanceof UserAddress) {
                throw new \Exception("Both addresses are required if you set an address.");
            }

            if ($nationalities !== null) {
                $data['nationalities'] = \array_map(
                    function ($nationality) {
                        return $nationality->getCountryCodeA3();
                    },
                    $nationalities
                );
            }

            $userData = $this->client->post(
                'users',
                [
                    RequestOptions::FORM_PARAMS => $data,
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

    /**
     * @param RegisterUserCommand $command
     *
     * @return int
     * @throws UserAlreadyExists
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function registerWithFullInfos(RegisterUserCommand $command): int
    {
        $title = $command->getTitle() instanceof UserTitle ? $command->getTitle()->getValue() : null;
        $birthday = $command->getBirthday() instanceof \DateTimeInterface
            ? $command->getBirthday()->format(self::BIRTHDAY_FORMAT)
            : null;

        try {
            $userData = $this->client->post(
                'users',
                [
                    RequestOptions::FORM_PARAMS => $this->filterPayload(
                        [
                            'email' => $command->getEmail(),
                            'password' => $command->getPassword(),
                            'firstName' => $command->getFirstName(),
                            'lastName' => $command->getLastName(),
                            'title' => $title,
                            'phone' => $command->getPhone(),
                            'lang' => $command->getLanguage(),
                            'birthday' => $birthday,
                            'billing' => self::serializeUserAddressUpdate($command->getBilling()),
                            'shipping' => self::serializeUserAddressUpdate($command->getShipping()),
                            'externalIdentifier' => $command->getExternalIdentifier(),
                            'isProfessional' => $command->getIsProfessional(),
                            'intraEuropeanCommunityVAT' => $command->getIntraEuropeanCommunityVAT(),
                            'company' => $command->getCompany(),
                            'jobTitle' => $command->getJobTitle(),
                            'comment' => $command->getComment(),
                            'legalIdentifier' => $command->getLegalIdentifier(),
                            'loyaltyIdentifier' => $command->getLoyaltyIdentifier(),
                            'nationalities' => $command->getCodesA3FromNationalities(),
                            'extra' => $command->getExtra(),
                        ]
                    ),
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

    public function registerPartially(RegisterUserCommand $command): int
    {
        try {
            $userData = $this->client->post(
                'users',
                [
                    RequestOptions::FORM_PARAMS => $this->filterPayload(
                        [
                            'email' => $command->getEmail(),
                            'password' => $command->getPassword(),
                            'firstName' => $command->getFirstName(),
                            'lastName' => $command->getLastName(),
                            'title' => \is_null($command->getTitle()) ? null : $command->getTitle()->getValue(),
                            'phone' => $command->getPhone(),
                            'lang' => $command->getLanguage(),
                            'birthday' => \is_null($command->getBirthday()) ? null : $command->getBirthday()->format(self::BIRTHDAY_FORMAT),
                            'billing' => \is_null($command->getBilling()) ? null : self::serializeUserAddressUpdate($command->getBilling()),
                            'shipping' => \is_null($command->getShipping()) ? null : self::serializeUserAddressUpdate($command->getShipping()),
                            'externalIdentifier' => $command->getExternalIdentifier(),
                            'isProfessional' => $command->getIsProfessional(),
                            'intraEuropeanCommunityVAT' => $command->getIntraEuropeanCommunityVAT(),
                            'company' => $command->getCompany(),
                            'jobTitle' => $command->getJobTitle(),
                            'comment' => $command->getComment(),
                            'legalIdentifier' => $command->getLegalIdentifier(),
                            'loyaltyIdentifier' => $command->getLoyaltyIdentifier(),
                            'nationalities' => $command->getCodesA3FromNationalities(),
                            'extra' => $command->getExtra(),
                        ]
                    ),
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

    /**
     * @param string            $email
     * @param UriInterface|null $recoverBaseUrl
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function recoverPassword(string $email, ?UriInterface $recoverBaseUrl = null)
    {
        $data = [
            'email' => $email,
        ];
        if (!empty($recoverBaseUrl)) {
            $data['recoverBaseUrl'] = to_string($recoverBaseUrl);
        }

        // On attend une 204 donc pas de retour
        $this->client->post(
            'users/password/recover',
            [
                RequestOptions::FORM_PARAMS => $data,
            ]
        );
    }

    /**
     * @param string $token
     * @param string $newPassword
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function changePasswordWithRecoveryToken(string $token, string $newPassword): void
    {
        $this->client->put(
            "users/password/change-with-token",
            [
                RequestOptions::JSON => [
                    'token' => $token,
                    'password' => $newPassword,
                ],
            ]
        );
    }

    /**
     * @param int    $userId
     * @param string $newPassword
     *
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function changePassword(int $userId, string $newPassword): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->put(
            "users/$userId/password",
            [
                RequestOptions::JSON => [
                    'password' => $newPassword,
                ],
            ]
        );
    }

    /**
     * Allow to enable a user
     *
     * @param int $userId
     *
     * @return void
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function enable(int $userId): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->post("users/{$userId}/enable");
    }

    /**
     * Allow to disable a user
     *
     * @param int $userId
     *
     * @return void
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function disable(int $userId): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->post("users/{$userId}/disable");
    }

    /**
     * @param int                     $userId
     * @param null|SubscriptionFilter $subscriptionFilter
     *
     * @return PaginatedData
     */
    public function listSubscriptionsBy(int $userId, SubscriptionFilter $subscriptionFilter = null): PaginatedData
    {
        $this->client->mustBeAuthenticated();

        if (false === $subscriptionFilter instanceof SubscriptionFilter) {
            $subscriptionFilter = (new SubscriptionFilter())
                ->setLimit(10)
                ->setOffset(0);
        }

        return $this->assertRessourceNotFound(
            function () use ($userId, $subscriptionFilter): PaginatedData {
                $response = $this->client->get(
                    "users/{$userId}/subscriptions",
                    [RequestOptions::QUERY => $subscriptionFilter->getFilters()]
                );

                return new PaginatedData(
                    $response['limit'],
                    $response['offset'],
                    $response['total'],
                    array_map(
                        function (array $subscription): SubscriptionSummary {
                            return new SubscriptionSummary($subscription);
                        },
                        $response['items']
                    )
                );
            },
            "User '{$userId}' not found."
        );
    }

    public function affiliateUser(string $email, int $companyId): array
    {
        $this->client->mustBeAuthenticated();
        try {
            return $this->client->post(
                "users/set-vendor",
                [
                    RequestOptions::JSON => [
                        'email' => $email,
                        'companyId' => $companyId
                    ],
                ]
            );
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

    public function disaffiliateUser(string $email): array
    {
        $this->client->mustBeAuthenticated();
        try {
            return $this->client->post(
                "users/set-vendor",
                [
                    RequestOptions::JSON => [
                        'email' => $email,
                        'companyId' => null
                    ],
                ]
            );
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 403:
                    throw new AccessDenied($e->getMessage());
                case 404:
                    throw new NotFound($e->getMessage());
                default:
                    throw $e;
            }
        }
    }

     /**
      * @param int $userId
      * @param string[]|int[] $extra
      *
      * @throws NotFound
      * @throws SomeParametersAreInvalid
      * @throws \GuzzleHttp\Exception\GuzzleException
      * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
      */
    public function deleteExtraFields(int $userId, array $extra): void
    {
        $this->client->mustBeAuthenticated();
        $this->client->delete(
            "users/$userId/extra",
            [
                RequestOptions::JSON => [
                    'extra' => $extra,
                ],
            ]
        );
    }

    /**
     * @param UpdateUserAddressCommand $command
     *
     * @return array
     */
    private static function serializeUserAddressUpdate(UpdateUserAddressCommand $command): array
    {
        $userAddress = [
            'label' => $command->getLabel(),
            'title' => \is_null($command->getTitle()) ? null : $command->getTitle()->getValue(),
            'firstname' => $command->getFirstName(),
            'lastname' => $command->getLastName(),
            'company' => $command->getCompany(),
            'phone' => $command->getPhone(),
            'address' => $command->getAddress(),
            'address_2' => $command->getAddressSecondLine(),
            'zipcode' => $command->getZipCode(),
            'city' => $command->getCity(),
            'country' => $command->getCountry(),
            'division_code' => $command->getDivisionCode(),
            'comment' => $command->getComment(),
        ];

        if ($command->getId() !== null) {
            $userAddress['address_id'] = $command->getId();
        }

        return $userAddress;
    }

    public function getUsersByFilters(UserFilters $userFilters): UsersPaginatedResult
    {
        try {
            $this->client->mustBeAuthenticated();
            $response = $this->client->get(
                'users',
                [
                    RequestOptions::QUERY => $userFilters->serialize()
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                throw new SomeParametersAreInvalid($e->getMessage(), $e->getResponse()->getStatusCode(), $e);
            }

            throw $e;
        }

        return new UsersPaginatedResult($response);
    }

    /**
     * Remove null values
     *
     * @param mixed[] $data
     *
     * @return mixed[]
     */
    private function filterPayload(array $data): array
    {
        return array_filter(
            $data,
            /** @param mixed $d */
            function ($d): bool {
                return false === \is_null($d);
            }
        );
    }
}
