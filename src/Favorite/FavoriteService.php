<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Favorite;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\AbstractService;
use Wizaplace\Favorite\Exception\CannotFavoriteDisabledOrInexistantDeclination;
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

    public function addToFavorite(ApiKey $apiKey, int $productId) : bool
    {
        try {
            $this->put('favorites/declinations/'.$productId, [], $apiKey);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $message = $e->getMessage();
            switch ($code) {
                case 400:
                    throw new CannotFavoriteDisabledOrInexistantDeclination($message, $code);
                    break;
                case 409:
                    throw new FavoriteAlreadyExist($message, $code);
                    break;
                default:
                    throw new \Exception($message, $code);
            }
        }

        return true;
    }

    public function removeFromFavorite($apiKey, int $productId) : bool
    {
        try {
            $this->delete('favorites/declinations/'.$productId, [], $apiKey);
        } catch (ClientException $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }

        return true;
    }
}
