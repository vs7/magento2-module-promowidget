<?php

namespace VS7\PromoWidget\Plugin;

class DataProvider
{
    protected $_bannerFactory;

    public function __construct(
        \VS7\PromoWidget\Model\BannerFactory $bannerFactory
    )
    {
        $this->_bannerFactory = $bannerFactory;
    }

    public function afterGetData(
        \Magento\SalesRule\Model\Rule\DataProvider $subject,
        $data
    )
    {
        $banner = $this->_bannerFactory->create();
        $ruleId = key($data);
        $banner->load($ruleId, 'rule_id');
        $data[$ruleId]['vs7_promowidget'] = array(
            'name' => $banner->getName(),
            'url_key' => $banner->getUrlKey(),
            'image' => $banner->getImage(),
            'position' => $banner->getPosition(),
            'text' => $banner->getText()
        );
        return $data;
    }
}
