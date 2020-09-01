<?php
namespace VS7\PromoWidget\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;


class Banner extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'vs7_promowidget_banner';

    protected $_cacheTag = 'vs7_promowidget_banner';

    protected $_eventPrefix = 'vs7_promowidget_banner';

    protected function _construct()
    {
        $this->_init('VS7\PromoWidget\Model\ResourceModel\Banner');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
