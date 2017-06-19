<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\MailingList;

use GuzzleHttp\Exception\ServerException;
use Wizaplace\AbstractService;
use Wizaplace\MailingList\Exception\UserAlreadySubscribed;

class MailingListService extends AbstractService
{
    public function getMailingList(): array
    {
        $mailingLists = $this->get('mailinglists');
        $list = [];

        foreach ($mailingLists as $mailingList) {
            $ml = new MailingList($mailingList['id'], $mailingList['name']);
            $list[] = $ml;
        }

        return $list;
    }

    public function subscribeToMailingList(int $mailingListId, string $email)
    {
        try {
            $this->post('mailinglists/'.$mailingListId.'/subscriptions/'.$email);
        } catch (ServerException $e) {
            throw new UserAlreadySubscribed();
        }
    }

    public function unsubscribeFromMailingList(int $mailingListId, string $email)
    {
        try {
            $this->delete('mailinglists/'.$mailingListId.'/subscriptions/'.$email);
        } catch (\Exception $e) {

        }
    }
}
