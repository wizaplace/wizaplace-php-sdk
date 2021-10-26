<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @license     Proprietary
 * @copyright   Copyright (c) Wizacha
 */

declare(strict_types=1);

namespace Wizaplace\SDK;

use Psr\Http\Message\ResponseInterface;

/**
 * If you want to listen to each request, you can create your own implementation of this interface
 * and inject it into the ApiClient constructor. It is easier than to extends the HttpClient.
 *
 * Example: if you want to listen to events in multiple places, your implementation could use the
 * Symfony event dispatcher.
 */
interface EventDispatcherInterface
{
    /**
     * Generates a unique id used to identify the event.
     * For instance :
     * return base64_encode(random_bytes(15));
     */
    public function getUniqueId(): string;

    /** This method is called before the Request. */
    public function dispatchRequestStart(string $eventId, string $method, string $uri, array &$params);

    /** This method is called after the Request. */
    public function dispatchRequestEnd(string $eventId, ?ResponseInterface $response = null);
}
