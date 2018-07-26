<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types=1);

namespace Wizaplace\SDK\Vendor\Promotion\Targets;

use Wizaplace\SDK\Vendor\Promotion\BasketPromotionTargetType;

interface BasketPromotionTarget
{
    /**
     * @internal for serialization purposes only
     */
    public function getType(): BasketPromotionTargetType;
}
