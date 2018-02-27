<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

namespace Wizaplace\SDK\Catalog;

use MyCLabs\Enum\Enum;

/**
 * @method static AttributeType FREE()
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
final class AttributeType extends Enum implements \JsonSerializable
{
    private const FREE = 'FREE';
    private const FREE_NUMBER = 'FREE_NUMBER';
    private const FREE_TEXT = 'FREE_TEXT';
    private const FREE_DATE = 'FREE_DATE';
    private const CHECKBOX_UNIQUE = 'CHECKBOX_UNIQUE'; // boolean
    private const LIST_TEXT = 'LIST_TEXT'; // single choice in a textual list
    private const LIST_NUMBER = 'LIST_NUMBER'; // single choice in a numerical list
    private const LIST_BRAND = 'LIST_BRAND'; // single choice in a brand list
    private const CHECKBOX_MULTIPLE = 'CHECKBOX_MULTIPLE'; // multiple choices in a list
    private const GROUP = 'GROUP'; // group of other attributes

    /**
     * @internal
     * @throws \UnexpectedValueException
     */
    public static function createFromLegacyMapping(string $value)
    {
        switch ($value) {
            case 'F':
                return self::FREE();
            case 'O':
                return self::FREE_NUMBER();
            case 'T':
                return self::FREE_TEXT();
            case 'D':
                return self::FREE_DATE();
            case 'C':
                return self::CHECKBOX_UNIQUE();
            case 'S':
                return self::LIST_TEXT();
            case 'N':
                return self::LIST_NUMBER();
            case 'E':
                return self::LIST_BRAND();
            case 'M':
                return self::CHECKBOX_MULTIPLE();
            case 'G':
                return self::GROUP();
            default:
                throw new \UnexpectedValueException("Value '$value' is not part of the legacy mapping for enum ".self::class);
        }
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize(): string
    {
        return $this->getValue();
    }
}
