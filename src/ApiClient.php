<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Jean85\PrettyVersions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
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
use Wizaplace\SDK\Exception\JsonDecodingError;
use Wizaplace\SDK\Exception\OrderNotFound;
use Wizaplace\SDK\Exception\ProductAttachmentNotFound;
use Wizaplace\SDK\Exception\ProductNotFound;
use Wizaplace\SDK\Exception\ReviewsAreDisabled;
use Wizaplace\SDK\Exception\SenderIsAlsoRecipient;
use Wizaplace\SDK\Favorite\Exception\FavoriteAlreadyExist;

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

    public function __construct(Client $client)
    {
        $this->httpClient = $client;

        try {
            $this->version = PrettyVersions::getVersion('wizaplace/sdk')->getPrettyVersion();
        } catch (\OutOfBoundsException $exception) {
            // Ignore error if version cannot be detected
            $this->version = 'unknown';
        }
    }

    /**
     * @throws BadCredentials
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
     * @throws BadCredentials
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

    public function getOAuthAuthorizationUrl(): string
    {
        return $this->get('user/oauth/authorize-url')['url'];
    }

    public function setApiKey(?ApiKey $apiKey = null): void
    {
        $this->apiKey = $apiKey;
    }

    public function getApiKey(): ?ApiKey
    {
        return $this->apiKey;
    }

    /**
     * @throws AuthenticationRequired
     */
    public function mustBeAuthenticated(): void
    {
        if (is_null($this->getApiKey())) {
            throw new AuthenticationRequired();
        }
    }

    public function get(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("GET", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    public function post(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("POST", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    public function put(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("PUT", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    public function delete(string $endpoint, array $options = [])
    {
        return $this->jsonDecode(
            $this->rawRequest("DELETE", $endpoint, $options)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * @param string|UriInterface $uri
     */
    public function rawRequest(string $method, $uri, array $options = []): ResponseInterface
    {
        $options[RequestOptions::HEADERS]['User-Agent'] = 'Wizaplace-PHP-SDK/'.$this->version;
        if ($this->language !== null) {
            $options[RequestOptions::HEADERS]['Accept-Language'] = $this->language;
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
                'Unable to parse JSON data: '.json_last_error_msg(),
                $lastJsonError
            );
        }

        return $data;
    }

    private function addAuth(array $options): array
    {
        if (!is_null($this->apiKey)) {
            $options['headers']['Authorization'] = 'token '.$this->apiKey->getKey();
        }

        return $options;
    }
}
