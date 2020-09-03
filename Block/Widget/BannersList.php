<?php

namespace VS7\PromoWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class BannersList extends Template implements BlockInterface
{
    protected $_template = "widget/list.phtml";
    protected $_banner;
    protected $_collection;

    public function __construct(
        Template\Context $context,
        \VS7\PromoWidget\Model\Banner $banner,
        array $data = []
    )
    {
        $this->_banner = $banner;
        $this->_collection = null;
        parent::__construct($context, $data);
    }

    public function getCollection()
    {
        if (!empty($this->_collection)) {
            return $this->_collection;
        }

        $banner = $this->_banner;
        $this->_collection = $banner->getCollection()
            ->addFieldToFilter('image', array('neq' => 'NULL'))
            ->addActiveRuleFilter()
            ->setOrder('position', 'desc');

        return $this->_collection;
    }
}
