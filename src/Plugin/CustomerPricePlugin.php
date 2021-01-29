<?php

namespace Itonomy\CustomerPrice\Plugin;

use Itonomy\CustomerPrice\Model\CustomerPrice;
use Itonomy\CustomerPrice\Model\ResourceModel\Price\Collection;
use Itonomy\CustomerPrice\Model\ResourceModel\Price\CollectionFactory;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use \Magento\Catalog\Model\Product\Type\Price;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class CustomerPricePlugin
{
    /**
     * @var CollectionFactory
     */
    protected $priceCollectionFactory;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    protected $config;

    /**
     * @var HttpContext $httpContext
     */
    protected $httpContext;
    /**
     * CustomerPricePlugin constructor.
     * @param CollectionFactory $priceCollectionFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     */
    public function __construct(
        CollectionFactory $priceCollectionFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        HttpContext $httpcontext
    ) {
        $this->priceCollectionFactory = $priceCollectionFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->httpContext = $httpcontext;
    }

    public function afterGetBasePrice(Price $subject, float $result, $product)
    {
        if($this->customerSession->isLoggedIn() || $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
            $customerId = $this->customerSession->getCustomerId() ?? $this->httpContext->getValue('customer_id');
            $websiteId = $this->getWebsiteForPriceScope();
            $productId = $product->getId();
            $finalPrice = (float) $product->getPrice();

            return min(
                $this->getCustomerPrice($productId,$customerId,$websiteId,$finalPrice),
                $result
            );
        } else {
            return $result;
        }
    }
    protected function getWebsiteForPriceScope()
    {
        $websiteId = 0;
        $value = $this->config->getValue('catalog/price/scope', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
        if ($value != 0) {
            // use the website associated with the current store
            $websiteId = $this->storeManager->getWebsite()->getId();
        }
        return $websiteId;
    }
    protected function getCustomerPrice(int $productId, int $customerId, int $websiteId, $finalPrice){
        /**
         * @var Collection $priceCollection
         */

        $priceCollection = $this->priceCollectionFactory->create();
        $priceObject = $priceCollection->getCustomerProductPrice($productId,$customerId,$websiteId);

        $price = null;

        if($priceObject){
            /**
             * @var CustomerPrice $priceObject
             */
            return(float) $priceObject->getPrice();
        } else {
            return $finalPrice;
        }
    }
}
