<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Seo;

use Clue\JsonStream\StreamingJsonParser;
use Wizaplace\SDK\AbstractService;
use Wizaplace\SDK\Exception\JsonDecodingError;
use function theodorejb\polycast\to_string;

final class SeoService extends AbstractService
{
    // All slugs should fully match this regexp
    public const SLUG_REGEXP = '[a-z0-9][a-z0-9-\.]*';

    /**
     * Takes several slugs and retrieves their targets.
     * @param string[] $slugs
     * @return (?SlugTarget)[] a map with this format: [slug => ?SlugTarget]
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
     */
    public function resolveSlug(string $slug): ?SlugTarget
    {
        return $this->resolveSlugs([$slug])[$slug] ?? null;
    }

    /**
     * @return iterable|SlugCatalogItem[]
     */
    public function listSlugs(): iterable
    {
        $response = $this->client->rawRequest('GET', 'seo/slugs/catalog');

        $parser = new StreamingJsonParser();

        $body = $response->getBody();

        // We read and ignore the first array's opening
        if ($body->read(1) !== '[') {
            throw new JsonDecodingError();
        }

        while (($c = $body->read(1)) !== '') {
            // we keep reading from the stream while we don't have a full object
            $data = $parser->push($c);
            if (empty($data)) {
                continue;
            }

            foreach ($data as $itemData) {
                try {
                    yield new SlugCatalogItem($itemData);
                } catch (\UnexpectedValueException $e) {
                    // we do not support all slug target types
                }
            }

            switch ($body->read(1)) {
                case ']': // end of the original array, we stop here
                    return;
                case ',': // new item, we keep going
                    break;
                default:
                    throw new JsonDecodingError();
            }
        }

        throw new JsonDecodingError(); // We should have found the end of the original array
    }
}
