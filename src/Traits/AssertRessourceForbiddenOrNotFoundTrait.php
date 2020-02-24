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

trait AssertRessourceForbiddenOrNotFoundTrait
{
    public function assertRessourceForbiddenOrNotFound(callable $callable, string $message403, string $message404)
    {
        try {
            return $callable();
        } catch (ClientException $exception) {
            if (403 === $exception->getResponse()->getStatusCode()) {
                throw new NotFound($message403, $exception);
            }

            if (404 === $exception->getResponse()->getStatusCode()) {
                throw new NotFound($message404, $exception);
            }

            throw $exception;
        }
    }
}
