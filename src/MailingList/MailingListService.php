<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\MailingList;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Wizaplace\AbstractService;
use Wizaplace\Exception\NotFound;
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
        $mailingLists = $this->get('mailinglists');
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
            $this->client->post('mailinglists/'.$mailingListId.'/subscriptions/'.$email);
        } catch (ClientException $e) {
            switch ($e->getCode()) {
                case 400:
                    throw new UserAlreadySubscribed();
                    break;
                case 404:
                    throw new MailingListDoesNotExist();
                    break;
                default:
                    break;
            }
        }
    }

    public function unsubscribe(int $mailingListId, string $email)
    {
        $this->client->delete('mailinglists/'.$mailingListId.'/subscriptions/'.$email);
    }
}
