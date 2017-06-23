<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Favorite;

use Wizaplace\AbstractService;
use Wizaplace\Favorite\Exception\CannotFavoriteDisabledOrInexistentDeclination;
use Wizaplace\Favorite\Exception\FavoriteAlreadyExist;
use Wizaplace\User\ApiKey;

class FavoriteService extends AbstractService
{
    public function isInFavorites(ApiKey $apiKey, int $declinationId) : bool
    {
        $results = $this->get('user/favorites/declinations', [], $apiKey);
        $isInFavorites = false;
        if (!empty($results)) {
            foreach ($results as $result) {
                $declination = explode('_', $result);
                if ($declination[0] == $declinationId) {
                    $isInFavorites = true;
                    break;
                }
            }
        }

        return $isInFavorites;
    }

    /**
     * @throws CannotFavoriteDisabledOrInexistentDeclination
     * @throws FavoriteAlreadyExist
     */
    public function addDeclinationToUserFavorites(ApiKey $apiKey, int $declinationId) : void
    {
        try {
            $this->post('user/favorites/declinations/'.$declinationId, [], $apiKey);
        } catch (\Exception $e) {
            $code = $e->getCode();
            switch ($code) {
                case CannotFavoriteDisabledOrInexistentDeclination::HTTP_ERROR_CODE:
                    throw new CannotFavoriteDisabledOrInexistentDeclination($declinationId, $e);
                    break;
                case FavoriteAlreadyExist::HTTP_ERROR_CODE:
                    throw new FavoriteAlreadyExist($declinationId, $e);
                    break;
                default:
                    throw $e;
            }
        }
    }

    public function removeDeclinationToUserFavorites(ApiKey $apiKey, int $declinationId) : void
    {
        $this->delete('user/favorites/declinations/'.$declinationId, [], $apiKey);
    }
}
