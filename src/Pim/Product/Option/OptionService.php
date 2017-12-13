<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Product\Option;

use Wizaplace\SDK\AbstractService;

final class OptionService extends AbstractService
{
    /**
     * @return Option[]
     * @throws \Wizaplace\SDK\Authentication\AuthenticationRequired
     */
    public function getCategoryOptions(int $categoryId): array
    {
        $this->client->mustBeAuthenticated();

        $data = $this->client->get("categories/${categoryId}/options");

        return array_map(function (array $optionData): Option {
            return new Option($optionData);
        }, $data);
    }
}
