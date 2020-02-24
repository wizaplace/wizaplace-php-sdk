<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Jean85\PrettyVersions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Psr\Log\LoggerInterface;
use Wizaplace\SDK\Authentication\ApiKey;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Authentication\BadCredentials;
use Wizaplace\SDK\Exception\BasketIsEmpty;
use Wizaplace\SDK\Exception\BasketNotFound;
use Wizaplace\SDK\Exception\CompanyHasNoAdministrator;
use Wizaplace\SDK\Exception\CompanyNotFound;
use Wizaplace\SDK\Exception\CouponCodeAlreadyApplied;
use Wizaplace\SDK\Exception\CouponCodeDoesNotApply;
use Wizaplace\SDK\Exception\DeclinationIsNotActive;
use Wizaplace\SDK\Exception\DiscussionNotFound;
use Wizaplace\SDK\Exception\DomainError;
use Wizaplace\SDK\Exception\ErrorCode;
use Wizaplace\SDK\Exception\InvalidPromotionRule;
use Wizaplace\SDK\Exception\JsonDecodingError;
use Wizaplace\SDK\Exception\OrderNotFound;
use Wizaplace\SDK\Exception\ProductAttachmentNotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\PromotionNotFound;
use Wizaplace\SDK\Exception\ReviewsAreDisabled;
use Wizaplace\SDK\Exception\SenderIsAlsoRecipient;
use Wizaplace\SDK\Favorite\Exception\FavoriteAlreadyExist;

/**
 * Class ApiClient
 * @package Wizaplace\SDK
 */
final class ApiClient
{
    /** @var Client */
    private $httpClient;

    /** @var null|ApiKey */
    private $apiKey;

    /** @var string */
    private $version;

    /** @var null|string */
    private $language;

    /** @var null|string */
    private $applicationToken;

    /** @var null|LoggerInterface */
    private $requestLogger;

    /**
     * ApiClient constructor.
     *
     * @param Client               $client
     * @param LoggerInterface|NULL $requestLogger
     */
    public function __construct(Client $client, LoggerInterface $requestLogger = null)
    {
        $this->httpClient = $client;
        $this->requestLogger = $requestLogger;

        try {
            $this->version = PrettyVersions::getVersion('wizaplace/sdk')->getPrettyVersion();
        } catch (\OutOfBoundsException $exception) {
            // Ignore error if version cannot be detected
            $this->version = 'unknown';
        }
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return ApiKey
     * @throws BadCredentials
     * @throws JsonDecodingError
     */
    public function authenticate(string $email, string $password): ApiKey
    {
        try {
            $apiKeyData = $this->get(
                'users/authenticate',
                [
                    'auth' => [$email, $password],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                throw new BadCredentials($e);
            }
            throw $e;
        }

        $apiKey = new ApiKey($apiKeyData);
        $this->setApiKey($apiKey);

        return $apiKey;
    }

    /**
     * @param string|null $applicationToken
     */
    public function setApplicationToken(?string $applicationToken): void
    {
        $this->applicationToken = $applicationToken;
    }

    /**
     * @param string $authToken
     *
     * @return ApiKey
     * @throws BadCredentials
     * @throws JsonDecodingError
     */
    public function oauthAuthenticate(string $authToken): ApiKey
    {
        try {
            $apiKeyData = $this->post(
                'user/oauth-token',
                [
                    RequestOptions::FORM_PARAMS => [
                        'token' => $authToken,
                    ],
                ]
            );
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 403) {
                throw new BadCredentials($e);
            }
            throw $e;
        }

        $apiKey = new ApiKey($apiKeyData);
        $this->setApiKey($apiKey);

        return $apiKey;
    }

    /**
     * @return string
     * @throws JsonDecodingError
     */
    public function getOAuthAuthorizationUrl(): string
    {
        return $this->get('user/oauth/authorize-url')['url'];
    }

    /**
     * @param ApiKey|null $apiKey
     */
    public function setApiKey(?ApiKey $apiKey = null): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return ApiKey|null
     */
    public function getApiKey(): ?ApiKey
    {
        return $this->apiKey;
    }

    /**
     * @throws AuthenticationRequired
     */
    public function mustBeAuthenticated(): void
    {
        if (\is_null($this->getApiKey())) {
            throw new AuthenticationRequired();
        }
    }

    /**
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed|null
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("GET", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed|null
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("POST", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed|null
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("PUT", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed|null
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("PATCH", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @param string $endpoint
     * @param array  $options
     *
     * @return mixed|null
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("DELETE", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @param string              $method
     * @param string|UriInterface $uri
     * @param array               $options
     *
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function rawRequest(string $method, $uri, array $options = []): ResponseInterface
    {
        $options[RequestOptions::HEADERS]['User-Agent'] = 'Wizaplace-PHP-SDK/' . $this->version;
        if ($this->language !== null) {
            $options[RequestOptions::HEADERS]['Accept-Language'] = $this->language;
        }

        if ($this->requestLogger !== null) {
            $logger = $this->requestLogger;
            $options[RequestOptions::ON_STATS] = function (TransferStats $stats) use ($logger) {
                $logger->info(
                    sprintf(
                        '%s %s %s %f',
                        $stats->getRequest()->getMethod(),
                        $stats->getRequest()->getUri(),
                        $_SERVER['HTTP_X_REQUEST_ID'] ?? '-',
                        $stats->getTransferTime()
                    )
                );
            };
        }

        if (!empty($_SERVER['HTTP_X_REQUEST_ID'])) {
            $options[RequestOptions::HEADERS]['X-Request-Id'] = $_SERVER['HTTP_X_REQUEST_ID'];
        }

        try {
            return $this->httpClient->request($method, $uri, $this->addAuth($options));
        } catch (BadResponseException $e) {
            $domainError = $this->extractDomainErrorFromGuzzleException($e);
            if ($domainError !== null) {
                throw $domainError;
            }

            throw $e;
        }
    }

    /**
     * @return UriInterface|null
     */
    public function getBaseUri(): ?UriInterface
    {
        return $this->httpClient->getConfig('base_uri');
    }

    /**
     * Changes the language the responses' content will be in.
     *
     * @param null|string $language the language code, for example 'fr' or 'en'
     */
    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    /**
     * @param BadResponseException $e
     *
     * @return DomainError|null
     */
    private function extractDomainErrorFromGuzzleException(BadResponseException $e): ?DomainError
    {
        try {
            $response = $this->jsonDecode($e->getResponse()->getBody()->getContents(), true);

            if (!isset($response['error'])) {
                return null;
            }

            $errorCode = new ErrorCode($response['error']['code']);

            $errorsClasses = [
                BasketNotFound::class,
                CouponCodeDoesNotApply::class,
                CouponCodeAlreadyApplied::class,
                ProductNotFound::class,
                ReviewsAreDisabled::class,
                SenderIsAlsoRecipient::class,
                CompanyHasNoAdministrator::class,
                CompanyNotFound::class,
                FavoriteAlreadyExist::class,
                BasketIsEmpty::class,
                DeclinationIsNotActive::class,
                ProductAttachmentNotFound::class,
                DiscussionNotFound::class,
                OrderNotFound::class,
                PromotionNotFound::class,
                InvalidPromotionRule::class,
            ];

            foreach ($errorsClasses as $errorClass) {
                if ($errorClass::getErrorCode()->equals($errorCode)) {
                    return new $errorClass($response['error']['message'], $data['error']['context'] ?? [], $e);
                }
            }

            return null;
        } catch (\Throwable $decodingError) {
            return null;
        }
    }

    /**
     * Does the same as json_decode(), but with proper error handling
     * @see \json_decode()
     *
     * @param string $json
     * @param bool   $assoc
     * @param int    $depth
     * @param int    $options
     *
     * @return mixed|null
     * @throws JsonDecodingError
     */
    private function jsonDecode(string $json, bool $assoc = true, int $depth = 512, int $options = 0)
    {
        if (empty($json)) {
            return null;
        }

        $data = \json_decode($json, $assoc, $depth, $options);

        $lastJsonError = json_last_error();
        if (JSON_ERROR_NONE !== $lastJsonError) {
            throw new JsonDecodingError(
                'Unable to parse JSON data: ' . json_last_error_msg(),
                $lastJsonError
            );
        }

        return $data;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function addAuth(array $options): array
    {
        if (!\is_null($this->apiKey)) {
            $options['headers']['Authorization'] = 'token ' . $this->apiKey->getKey();
        }
        if (!\is_null($this->applicationToken)) {
            $options['headers']['Application-Token'] = $this->applicationToken;
        }

        return $options;
    }
}
