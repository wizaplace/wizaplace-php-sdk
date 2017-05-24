<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\CMS;

use MyCLabs\Enum\Enum;

/**
 * @method static ObjectType PRODUCT()
 * @method static ObjectType CATEGORY()
 * @method static ObjectType ATTRIBUTE_VARIANT()
 * @method static ObjectType COMPANY()
 * @method static ObjectType CMS_PAGE()
 * @method static ObjectType REDIRECT()
 */
class ObjectType extends Enum
{
    private const PRODUCT = 'product';
    private const CATEGORY = 'category';
    private const ATTRIBUTE_VARIANT = 'attribute_variant';
    private const COMPANY = 'company';
    private const CMS_PAGE = 'cms_page';
    private const REDIRECT = 'redirect';
}
