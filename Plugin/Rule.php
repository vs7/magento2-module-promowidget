<?php

namespace VS7\PromoWidget\Plugin;

class Rule
{
    protected $_bannerFactory;

    public function __construct(
        \VS7\PromoWidget\Model\BannerFactory $bannerFactory
    )
    {
        $this->_bannerFactory = $bannerFactory;
    }

    public function afterSave(
        \Magento\SalesRule\Model\Rule $subject,
        $data
    )
    {
        $a = 1;
        return;
    }
}
