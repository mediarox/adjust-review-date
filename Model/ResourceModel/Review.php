<?php
/**
 * @package Mediarox_AdjustReviewDate
 * @copyright Copyright 2019 (c) mediarox UG (haftungsbeschraenkt) (http://www.mediarox.de)
 * @author Kristian Ernst <kernst@mediarox.de>
 */
namespace Mediarox\AdjustReviewDate\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * Review resource model
 *
 * @api
 * @since 100.0.2
 */
class Review extends \Magento\Review\Model\ResourceModel\Review
{
    /**
     * Perform actions before object save
     *
     * @param AbstractModel $object
     * @return $this
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt($this->_date->gmtDate());
        }
        if ($object->hasData('stores') && is_array($object->getStores())) {
            if($object->getDate()) {
                $object->setCreatedAt($object->getDate());
            }
            $stores = $object->getStores();
            $stores[] = 0;
            $object->setStores($stores);
        } elseif ($object->hasData('stores')) {
            $object->setStores([$object->getStores(), 0]);
        }
        return $this;
    }
}
