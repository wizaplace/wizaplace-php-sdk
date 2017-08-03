<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Discussion;

use Wizaplace\AbstractService;

class DiscussionService extends AbstractService
{
    /**
     * @return Discussion[]
     */
    public function getDiscussions(): array
    {
        $discussions = $this->client->get('discussions');

        return array_map(function($discussionData){
            new Discussion($discussionData);
        }, $discussions);
    }

    public function getDiscussion(int $discutionId): Discussion
    {
        return new Discussion($this->client->get('discussions/'.$discutionId));
    }

    public function startDiscussion(int $productId): Discussion
    {
        try {
            $discussionData = $this->client->post('discussions', ['json' => ['productId' => $productId]]);
        } catch (\Exception $e) {
            throw $e;
        }

        return new Discussion($discussionData);
    }
}
