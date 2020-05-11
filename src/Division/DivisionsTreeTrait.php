<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Division;

use GuzzleHttp\RequestOptions;

/** Common methods for divisions services */
trait DivisionsTreeTrait
{
    /** @return Division[] a Division tree, see item's `parent` and `children` properties to navigate in the tree */
    public function getDivisionsTreeByUrl(string $url, DivisionsTreeFilters $divisionsTreeFilters = null): array
    {
        $this->client->mustBeAuthenticated();

        $normalizedTree = $this->client->get(
            $url,
            [
                RequestOptions::QUERY =>
                    $divisionsTreeFilters instanceof DivisionsTreeFilters
                        ? $divisionsTreeFilters->toArray()
                        : []
                ,
            ]
        );

        $denormalizedTree = [];
        if (\is_array($normalizedTree)) {
            foreach ($normalizedTree as $item) {
                $denormalizedTree[] = new Division($item);
            }
        }

        return $denormalizedTree;
    }
}
