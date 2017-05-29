<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Seo;

use Wizaplace\AbstractService;

class SeoService extends AbstractService
{
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

        $rawResults = $this->get('seo/slugs', ['query' => ['slugs' => $slugs]]);

        $results = [];
        foreach ($slugs as $slug) {
            if (!isset($rawResults[$slug])) {
                $results[$slug] = null;
            } else {
                $results[$slug] = new SlugTarget(new SlugTargetType($rawResults[$slug]['type']), (string) $rawResults[$slug]['id']);
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
}
