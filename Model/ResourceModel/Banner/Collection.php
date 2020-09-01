<?php

namespace VS7\PromoWidget\Model\ResourceModel\Banner;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'banner_id';
    protected $_eventPrefix = 'vs7_promowidget_banner_collection';
    protected $_eventObject = 'banner_collection';

    protected function _construct()
    {
        $this->_init('VS7\PromoWidget\Model\Banner', 'VS7\PromoWidget\Model\ResourceModel\Banner');
    }
}
