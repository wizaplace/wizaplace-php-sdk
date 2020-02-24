<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Traits;

use GuzzleHttp\Exception\ClientException;
use Wizaplace\SDK\Exception\NotFound;

trait AssertRessourceNotFoundTrait
{
    public function assertRessourceNotFound(callable $callable, string $message)
    {
        try {
            return $callable();
        } catch (ClientException $exception) {
            if ($exception->getResponse()->getStatusCode() === 404) {
                throw new NotFound($message, $exception);
            }

            throw $exception;
        }
    }
}
