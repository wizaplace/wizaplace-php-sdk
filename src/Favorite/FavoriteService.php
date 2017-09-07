<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Favorite;

use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Catalog\DeclinationSummary;
use Wizaplace\SDK\Favorite\Exception\CannotFavoriteDisabledOrInexistentDeclination;
use Wizaplace\SDK\Favorite\Exception\FavoriteAlreadyExist;

/**
 * This service helps managing the favorite products of a user.
 */
final class FavoriteService extends AbstractService
{
    /**
     * Return all the products saved as favorites
     *
     * @throws AuthenticationRequired
     *
     * @return DeclinationSummary[]
     */
    public function getAll() : array
    {
        $this->client->mustBeAuthenticated();
        $results = $this->client->get('user/favorites/declinations', []);

        return array_map(static function (array $favorite) : DeclinationSummary {
            return new DeclinationSummary($favorite);
        }, $results['_embedded']['favorites']);
    }

    /**
     * Check whether a product is in the user's favorites.
     *
     * @throws AuthenticationRequired
     */
    public function isInFavorites(string $declinationId) : bool
    {
        $this->client->mustBeAuthenticated();
        $results = $this->client->get('user/favorites/declinations/ids', []);
        $isInFavorites = false;
        if ($results['count'] > 0) {
            foreach ($results['_embedded']['favorites'] as $result) {
                if ($result == $declinationId) {
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
    public function addDeclinationToUserFavorites(string $declinationId) : void
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
    public function removeDeclinationToUserFavorites(string $declinationId) : void
    {
        $this->client->mustBeAuthenticated();
        $this->client->rawRequest('delete', 'user/favorites/declinations/'.$declinationId);
    }
}
