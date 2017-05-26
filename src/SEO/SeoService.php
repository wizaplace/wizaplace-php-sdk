<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SEO;

use Wizaplace\AbstractService;

class SeoService extends AbstractService
{
    /**
     * @param string[] $slugs
     * @return (?SlugTarget)[] slug => ?SlugTarget
     */
    public function resolveSlugs(array $slugs): array
    {
        if (empty($slugs)) {
            return [];
        }

        $slugs = array_map('strval', $slugs);

        $rawResults = $this->get("seo/slugs", ['query' => ['slugs' => $slugs]]);

        $results = [];
        foreach ($slugs as $slug) {
            if (!isset($rawResults[$slug])) {
                $results[$slug] = null;
            } else {
                $results[$slug] = new SlugTarget(new SlugTargetType($rawResults[$slug]['type']), (int) $rawResults[$slug]['id']);
            }
        }

        return $results;
    }

    public function resolveSlug(string $slug): ?SlugTarget
    {
        return $this->resolveSlugs([$slug])[$slug] ?? null;
    }
}
