<?php

namespace VS7\PromoWidget\Model\ResourceModel\Banner;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'banner_id';
    protected $_eventPrefix = 'vs7_promowidget_banner_collection';
    protected $_eventObject = 'banner_collection';

    protected $_timezone;
    protected $_storeManager;
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        $this->_timezone = $timezone;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init('VS7\PromoWidget\Model\Banner', 'VS7\PromoWidget\Model\ResourceModel\Banner');
    }

    public function addActiveRuleFilter()
    {
        $now = $this->_timezone->date()->format('Y-m-d');

        $this->getSelect()
            ->joinLeft(
                array('rules' => $this->getTable('salesrule')),
                'main_table.rule_id=rules.rule_id',
                array()
            )
            ->joinLeft(
                array('website' => $this->getTable('salesrule_website')),
                'rules.rule_id=website.rule_id',
                array()
            )
            ->joinLeft(
                array('customer_group' => $this->getTable('salesrule_customer_group')),
                'rules.rule_id=customer_group.rule_id',
                array()
            )
            ->where('rules.from_date is null or rules.from_date <= ?', $now)
            ->where('rules.to_date is null or rules.to_date >= ?', $now)
            ->where('rules.is_active = 1')
            ->where('website.website_id = ?', $this->_storeManager->getStore()->getWebsiteId())
            ->where('customer_group.customer_group_id = ?', $this->_customerSession->getCustomer()->getGroupId());

        return $this;
    }
}
