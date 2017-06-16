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

    public function addToFavorite(ApiKey $apiKey, int $productId) : int
    {
        try {
            $this->put('favorites/declinations/'.$productId, [], $apiKey);
        } catch (\Exception $e) {
            $code = $e->getCode();
        }
        if (!isset($code)) {
            $code = 201;
        }

        return $code;
    }

    public function removeFromFavorite($apiKey, int $productId) : int
    {
        try {
            $code = $this->delete('favorites/declinations/'.$productId, [], $apiKey);
        } catch (ClientException $ex) {
            $code = $ex->getResponse()->getStatusCode();
        }
        if (!isset($code)) {
            $code = 204;
        }

        return $code;
    }
}
