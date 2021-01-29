<?php


namespace Itonomy\CustomerPrice\Pricing\Price;

use Itonomy\CustomerPrice\Model\ResourceModel\Price\CollectionFactory;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;
use Magento\Framework\Pricing\Price\BasePriceProviderInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Customer price model
 */
class CustomerPrice extends AbstractPrice implements BasePriceProviderInterface
{
    /**
     * Default price type
     */
    const PRICE_CODE = 'customer_price';

    /**
     * @var CollectionFactory $customerPriceCollection
     */
    protected $customerPriceCollection;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Context  $httpContext
     */
    protected $httpContext;

    /**
     * CustomerPrice constructor.
     * @param Product $saleableItem
     * @param $quantity
     * @param CalculatorInterface $calculator
     * @param PriceCurrencyInterface $priceCurrency
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Session $customerSession
     */
    public function __construct(
        Product $saleableItem,
        $quantity,
        CalculatorInterface $calculator,
        PriceCurrencyInterface $priceCurrency,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        Context $httpContext
    )
    {
        parent::__construct($saleableItem, $quantity, $calculator, $priceCurrency);
        $this->customerPriceCollection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
    }

    /**
     * Get price value
     *
     * @return float
     */
    public function getValue()
    {
        if (null === $this->value) {
            if ($this->product->hasData(self::PRICE_CODE)) {
                $value = $this->product->getData(self::PRICE_CODE);
                $this->value = $value ? (float)$value : false;
            } else {
                if($this->customerSession->isLoggedIn() || $this->httpContext->getValue(CustomerContext::CONTEXT_AUTH)) {
                    $priceObject = $this->customerPriceCollection->getCustomerProductPrice(
                        $this->product->getId(),
                        $customerId = $this->customerSession->getCustomerId() ?? $this->httpContext->getValue('customer_id'),
                        $this->storeManager->getStore()->getWebsiteId()
                    );
                }
                $this->value = isset($priceObject) ? (float)$priceObject->getPrice() : false;
            }
            if ($this->value) {
                $this->value = $this->priceCurrency->convertAndRound($this->value);
            }
        }
        return $this->value;
    }
}
