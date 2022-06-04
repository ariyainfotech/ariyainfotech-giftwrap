<?php

namespace AriyaInfoTech\GiftWrap\Model\GiftWrap;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\LayoutInterface;

class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
	
	private $_giftHelper;
	
	protected $_layout;

    public function __construct(
        Session $customerSession,
        CheckoutSession $checkoutSession,
        PriceCurrencyInterface $priceCurrency,
        StoreManagerInterface $storeManager,
		\AriyaInfoTech\GiftWrap\Helper\Data $giftHelper,
		LayoutInterface $layout
    ) {
		$this->_layout = $layout;	
        $this->customerSession = $customerSession;
        $this->quote = $checkoutSession->getQuote();
        $this->priceCurrency = $priceCurrency;
        $this->storeManager = $storeManager;
		$this->_giftHelper = $giftHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(){
		
		$isvisable = false;
		$isgiftCard = false;
		
		if($this->_giftHelper->isCheckoutShowSection() == 1){
			$isvisable = true;
		}
		
		if($this->_giftHelper->isanyProductAllReadyInGiftWrap()){
			$isgiftCard = true;
		}
		
        $result = [];		
        $result['giftwrap'] = [
            'isVisible' => $isvisable,
			'isDefautCheckoxSelectd' => $isgiftCard,
			'my_block_content' => $this->_layout->createBlock('Magento\Framework\View\Element\Template')->setTemplate('AriyaInfoTech_GiftWrap::checkout/seller-product.phtml')->toHtml()
        ];

        return $result;
    }
}