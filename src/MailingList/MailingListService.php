<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\MailingList;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\MailingList\Exception\MailingListDoesNotExist;
use Wizaplace\SDK\MailingList\Exception\UserAlreadySubscribed;

/**
 * Manages mailing lists and subscriptions
 */
final class MailingListService extends AbstractService
{
    /**
     * @return MailingList[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function getMailingLists(): array
    {
        $mailingLists = $this->client->get('mailinglists');
        $lists = [];

        foreach ($mailingLists as $mailingList) {
            $lists[] = new MailingList($mailingList['id'], $mailingList['name']);
        }

        return $lists;
    }

    /**
     * @param int    $mailingListId
     * @param string $email
     *
     * @throws MailingListDoesNotExist
     * @throws UserAlreadySubscribed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function subscribe(int $mailingListId, string $email)
    {
        try {
            $this->client->rawRequest('post', 'mailinglists/'.$mailingListId.'/subscriptions/'.$email);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 404:
                    throw new MailingListDoesNotExist("Mailing list #{$mailingListId} does not exist", $e);
                case 409:
                    throw new UserAlreadySubscribed("User '{$email}' already subscribed to mailing list #{$mailingListId}", 409, $e);
                default:
                    throw $e;
            }
        }
    }

    /**
     * @param int    $mailingListId
     * @param string $email
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function unsubscribe(int $mailingListId, string $email)
    {
        $this->client->delete("mailinglists/$mailingListId/subscriptions/$email");
    }

    /**
     * Check if the current authenticated user is subscribed
     *
     * @param int $mailingListId
     *
     * @return bool
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function isSubscribed(int $mailingListId): bool
    {
        $this->client->mustBeAuthenticated();

        $response = $this->client->get("mailinglists/{$mailingListId}/subscription");

        return (bool) $response['isSubscribedTo'];
    }
}
