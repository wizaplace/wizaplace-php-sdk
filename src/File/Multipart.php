<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
namespace Wizaplace\SDK\File;

use Wizaplace\SDK\ArrayableInterface;

/**
 * Class Multipart
 * @package Wizaplace\SDK\File
 */
class Multipart
{
    /**
     * @param array $data
     * @param array $files
     *
     * @return array
     */
    public static function createMultipartArray(array $data, array $files) : array
    {
        $dataToSend = [];

        $flatArray = self::flattenArray($data);

        foreach ($flatArray as $key => $value) {
            $dataToSend[] = [
                'name'  => $key,
                'contents' => $value,
            ];
        }

        foreach ($files as $file) {
            $dataToSend[] = [
                'name' => $file->getName(),
                'contents' => $file->getContents(),
            ];
        }

        return $dataToSend;
    }

    /**
     * This method help to have an array compliant to Guzzle for multipart POST/PUT for the organisation process
     *
     * Ex:
     * ['name' => 'obiwan', ['address' => ['street' => 'main street', 'city' => 'Mos Esley']]
     * needs to be flatten to
     * ['name' => 'obiwan', 'address[street]' => 'main street', 'address[city]' => 'Mos esley']
     *
     * @param array $array
     * @param string $originalKey
     * @return array
     */
    private static function flattenArray(array $array, string $originalKey = '')
    {
        $output = [];

        foreach ($array as $key => $value) {
            $newKey = $originalKey;
            if (empty($originalKey)) {
                $newKey .= $key;
            } else {
                $newKey .= '['.$key.']';
            }

            if (is_array($value)) {
                $output = array_merge($output, self::flattenArray($value, $newKey));
            } elseif ($value instanceof ArrayableInterface) {
                $output = array_merge($output, self::flattenArray($value->toArray(), $newKey));
            } else {
                $output[$newKey] = $value;
            }
        }

        return $output;
    }
}
