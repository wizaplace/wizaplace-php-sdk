<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Catalog;

use MyCLabs\Enum\Enum;

/**
 * @method static Condition BRAND_NEW()
 * @method static Condition USED()
 */
final class Condition extends Enum implements \JsonSerializable
{
    private const BRAND_NEW = 'N';
    private const USED = 'U';

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): string
    {
        return $this->getValue();
    }
}
