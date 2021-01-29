<?php


namespace Itonomy\CustomerPrice\Model;


class CustomerPrice extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Itonomy\CustomerPrice\Model\ResourceModel\Price');
    }
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
    public function getCustomerId(){
        return $this->getData('customer_id');
    }
    public function getProductId(){
        return $this->getData('product_id');
    }
    public function getWebsiteId(){
        return $this->getData('website_id');
    }
    public function getPrice(){
        return $this->getData('customer_price');
    }
}
