<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Exim;

use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\RequestOptions;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Authentication\AuthenticationRequired;
use Wizaplace\SDK\Exception\FileNotFound;

/**
 * Class EximService
 * @package Wizaplace\SDK\Exim
 */
final class EximService extends AbstractService
{
    /**
     * @throws AuthenticationRequired
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Wizaplace\SDK\Exception\FileNotFound
     * @throws \Wizaplace\SDK\Exception\JsonDecodingError
     */
    public function importProducts(string $filePath): string
    {
        $this->client->mustBeAuthenticated();

        // Open CSV file
        // We need the @ the catch the exception ourself in dev mode
        $file = @fopen($filePath, 'r+');
        if (false === $file) {
            throw new FileNotFound('File not found ' . $filePath, ['file' => $filePath]);
        }

        // Add stream to HTTP body
        $stream = new Stream($file);
        $data[] = [
            'name'     => 'file',
            'contents' => $stream,
            'filename' => 'product.csv',
        ];

        // Send CSV file to API
        $data = $this->client->post(
            'import/products',
            [
                RequestOptions::MULTIPART => $data,
            ]
        );

        return $data['jobId'];
    }
}
