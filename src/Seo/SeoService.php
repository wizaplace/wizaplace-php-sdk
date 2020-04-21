<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Seo;

use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\JsonDecodingError;

use function theodorejb\polycast\to_string;

/**
 * Class SeoService
 * @package Wizaplace\SDK\Seo
 */
final class SeoService extends AbstractService
{
    private const OFFSET = 0;
    private const LIMIT = 100;

    // All slugs should fully match this regexp
    public const SLUG_REGEXP = '[a-z0-9][a-z0-9-\.]*';

    /**
     * Takes several slugs and retrieves their targets.
     *
     * @param string[] $slugs
     *
     * @return array (?SlugTarget)[] a map with this format: [slug => ?SlugTarget]
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resolveSlugs(array $slugs): array
    {
        if (empty($slugs)) {
            return [];
        }

        $slugs = array_map('strval', $slugs);

        $rawResults = $this->client->get('seo/slugs', ['query' => ['slugs' => $slugs]]);

        $results = [];
        foreach ($slugs as $slug) {
            if (!isset($rawResults[$slug])) {
                $results[$slug] = null;
            } else {
                $results[$slug] = new SlugTarget(new SlugTargetType($rawResults[$slug]['type']), to_string($rawResults[$slug]['id']));
            }
        }

        return $results;
    }

    /**
     * Retrieves the target of one slug.
     *
     * @param string $slug
     *
     * @return SlugTarget|null
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resolveSlug(string $slug): ?SlugTarget
    {
        return $this->resolveSlugs([$slug])[$slug] ?? null;
    }

    /**
     * @return array
     *
     * @param null|int $offset
     * @param null|int $limit
     *
     * @throws JsonDecodingError
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listSlugs(int $offset = null, int $limit = null): array
    {
        if (\is_integer($offset) === false || \is_integer($limit) === false) {
            $response = $this->client->get('seo/slugs/list');
        } else {
            $response = $this->client->get('seo/slugs/list?offset=' . $offset . '&limit=' . $limit);
        }

        $arrayItems = [];
        foreach ($response['items'] as $itemData) {
            try {
                $arrayItems[] =  new SlugCatalogItem($itemData);
            } catch (\UnexpectedValueException $e) {
                // we do not support all slug target types
            }
        }

        $response['items'] = $arrayItems;

        return $response;
    }
}
