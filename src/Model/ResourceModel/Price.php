<?php


namespace Itonomy\CustomerPrice\Model\ResourceModel;


class Price extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('itonomy_customerprice', 'entity_id');
    }
}
