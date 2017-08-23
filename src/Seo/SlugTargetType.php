<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\Seo;

use MyCLabs\Enum\Enum;

/**
 * @method static SlugTargetType PRODUCT()
 * @method static SlugTargetType CATEGORY()
 * @method static SlugTargetType ATTRIBUTE_VARIANT()
 * @method static SlugTargetType COMPANY()
 * @method static SlugTargetType CMS_PAGE()
 */
final class SlugTargetType extends Enum
{
    private const PRODUCT = 'product';
    private const CATEGORY = 'category';
    private const ATTRIBUTE_VARIANT = 'attribute_variant';
    private const COMPANY = 'company';
    private const CMS_PAGE = 'cms_page';
}
