<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */

namespace Wizaplace\SDK\Division;

/**
 * Class DivisionUtils
 * @package Wizaplace\SDK\Division
 *
 * @internal
 */
class DivisionUtils
{
    /** @var array */
    private $divisions;

    /**
     * @param array $availableOffers
     * @param bool  $haveProductIds
     *
     * @return array
     */
    public function getDivisions(array $availableOffers, $haveProductIds = false)
    {
        if ($haveProductIds) {
            foreach ($availableOffers as $productId => $ao) {
                $this->createDivisions($ao, $productId);
            }

            foreach ($this->divisions as $key => $divisions) {
                $this->divisions[$key] = $this->imbricate($divisions);
            }
        } else {
            $this->createDivisions($availableOffers, null);
            $this->divisions = $this->imbricate($this->divisions);
        }

        return array_values($this->divisions);
    }

    /**
     * @param array    $availableOffers
     * @param null|int $productId
     */
    private function createDivisions(array $availableOffers, ?int $productId)
    {
        $keys = array_keys($availableOffers);

        if (is_numeric($keys[0])) {
            foreach ($availableOffers as $availableOffer) {
                if (is_null($productId)) {
                    $this->divisions[] = new Division($availableOffer);
                } else {
                    $this->divisions[$productId][] = new Division($availableOffer);
                }

                if (isset($availableOffer['children']) && !empty($availableOffer['children'])) {
                    $this->createDivisions($availableOffer['children'], $productId);
                }
            }
        } else {
            $this->divisions[] = new Division($availableOffers);
            if (isset($availableOffers['children']) && !empty($availableOffers['children'])) {
                $this->createDivisions($availableOffers['children'], $productId);
            }
        }
    }

    /**
     * @param Division[] $divisions
     *
     * @return array
     */
    private function imbricate($divisions): array
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

                    unset($divisions[$key]);
                }
            }
        }

        return $divisions;
    }
}
