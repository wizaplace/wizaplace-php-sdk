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
    public function isFavorite(ApiKey $apiKey, int $productId) : bool
    {
        $results = $this->get('favorites/declinations', [], $apiKey);
        $isFavorite = false;
        if (!empty($results)) {
            foreach ($results as $result) {
                $product = explode('_', $result);
                if ($product[0] == $productId) {
                    $isFavorite = true;
                    break;
                }
            }
        }

        return $isFavorite;
    }

    public function addToFavorite(ApiKey $apiKey, int $productId) : void
    {
        try {
            $this->put('favorites/declinations/'.$productId, [], $apiKey);
        } catch (\Exception $e) {
            $code = $e->getCode();
            switch ($code) {
                case CannotFavoriteDisabledOrInexistentDeclination::HTTP_ERROR_CODE:
                    throw new CannotFavoriteDisabledOrInexistentDeclination($productId, $e);
                    break;
                case FavoriteAlreadyExist::HTTP_ERROR_CODE:
                    throw new FavoriteAlreadyExist($productId, $e);
                    break;
                default:
                    throw $e;
            }
        }
    }

    public function removeFromFavorite(ApiKey $apiKey, int $productId) : void
    {
        $this->delete('favorites/declinations/'.$productId, [], $apiKey);
    }
}
