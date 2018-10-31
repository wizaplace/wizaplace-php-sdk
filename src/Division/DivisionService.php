<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Division;

class DivisionService
{
    /**
     * Imbricate array to get a tree of divisions
     *
     * @param Division[] $divisions
     *
     * @return object[]
     */
    public static function imbricate(array $divisions) : array
    {
        /** @var Division[] $objectsById */
        $objectsById = array();
        foreach ($divisions as $d) {
            $objectsById[$d->getCode()] = $d;
        }

        foreach ($divisions as $key => $d) {
            if (!is_null($d->getParentCode())) {
                if (isset($objectsById[$d->getParentCode()])) {
                    // Ajout de l'enfant dans le parent
                    $objectsById[$d->getParentCode()]->addChild($d);
                }

                unset($divisions[$key]);
            }
        }

        return $divisions;
    }
}
