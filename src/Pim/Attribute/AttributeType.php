<?php

/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

declare(strict_types=1);

namespace Wizaplace\SDK\Pim\Attribute;

use MyCLabs\Enum\Enum;

/**
 * Class AttributeType
 * @package Wizaplace\SDK\Pim\Attribute
 *
 * @method static AttributeType FREE_NUMBER()
 * @method static AttributeType FREE_TEXT()
 * @method static AttributeType FREE_DATE()
 * @method static AttributeType LIST_NUMBER()
 * @method static AttributeType LIST_TEXT()
 * @method static AttributeType LIST_BRAND()
 * @method static AttributeType CHECKBOX_UNIQUE()
 * @method static AttributeType CHECKBOX_MULTIPLE()
 * @method static AttributeType GROUP()
 */
final class AttributeType extends Enum
{
    private const FREE_NUMBER = 'O';
    private const FREE_TEXT = 'T';
    private const FREE_DATE = 'D';
    private const CHECKBOX_UNIQUE = 'C'; // boolean
    private const LIST_TEXT = 'S'; // single choice in a textual list
    private const LIST_NUMBER = 'N'; // single choice in a numerical list
    private const LIST_BRAND = 'E'; // single choice in a brand list
    private const CHECKBOX_MULTIPLE = 'M'; // multiple choices in a list
    private const GROUP = 'G'; // group of other attributes
}
