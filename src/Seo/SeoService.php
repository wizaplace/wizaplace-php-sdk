<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Seo;

use Wizaplace\SDK\AbstractService;
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
     * @return SlugCatalogItem[]
     */
    public function listSlugs(): array
    {
        $slugsCatalog = $this->client->get('seo/slugs/catalog');


        return array_filter(array_map(static function (array $itemData): ?SlugCatalogItem {
            try {
                return new SlugCatalogItem($itemData);
            } catch (\UnexpectedValueException $e) {
                return null; // we do not support all slug target types
            }
        }, $slugsCatalog));
    }
}
