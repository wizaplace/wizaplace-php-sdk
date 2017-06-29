<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Favorite;

use Wizaplace\AbstractService;
use Wizaplace\Authentication\AuthenticationRequired;
use Wizaplace\Favorite\Exception\CannotFavoriteDisabledOrInexistentDeclination;
use Wizaplace\Favorite\Exception\FavoriteAlreadyExist;

/**
 * This service helps managing the favorite products of a user.
 */
class FavoriteService extends AbstractService
{
    /**
     * Check whether a product is in the user's favorites.
     *
     * @throws AuthenticationRequired
     */
    public function isInFavorites(int $declinationId) : bool
    {
        $this->client->mustBeAuthenticated();
        $results = $this->client->get('user/favorites/declinations', []);
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
     * @throws AuthenticationRequired
     * @throws CannotFavoriteDisabledOrInexistentDeclination
     * @throws FavoriteAlreadyExist
     */
    public function addDeclinationToUserFavorites(int $declinationId) : void
    {
        $this->client->mustBeAuthenticated();
        try {
            $this->client->rawRequest('post', 'user/favorites/declinations/'.$declinationId);
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

    /**
     * @throws AuthenticationRequired
     */
    public function removeDeclinationToUserFavorites(int $declinationId) : void
    {
        $this->client->mustBeAuthenticated();
        $this->client->rawRequest('delete', 'user/favorites/declinations/'.$declinationId);
    }
}
