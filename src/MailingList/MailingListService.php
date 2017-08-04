<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\MailingList;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\AbstractService;
use Wizaplace\MailingList\Exception\MailingListDoesNotExist;
use Wizaplace\MailingList\Exception\UserAlreadySubscribed;

/**
 * Manages mailing lists and subscriptions
 */
class MailingListService extends AbstractService
{
    /**
     * @return MailingList[]
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
     * @throws UserAlreadySubscribed
     */
    public function subscribe(int $mailingListId, string $email)
    {
        try {
            $this->client->rawRequest('post', 'mailinglists/'.$mailingListId.'/subscriptions/'.$email);
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 404:
                    throw new MailingListDoesNotExist("Mailing list #{$mailingListId} does not exist", $e);
                    break;
                case 409:
                    throw new UserAlreadySubscribed("User '{$email}' already subsribed to mailing list #{$mailingListId}", 409, $e);
                    break;
                default:
                    throw $e;
                    break;
            }
        }
    }

    public function unsubscribe(int $mailingListId, string $email)
    {
        $this->client->delete("mailinglists/$mailingListId/subscriptions/$email");
    }
}
