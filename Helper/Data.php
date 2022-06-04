<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AriyaInfoTech\GiftWrap\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Checkout\Model\Session;

class Data extends AbstractHelper
{
	
	protected $scopeConfig;
	
	protected $quoteRepository;
	
	protected $cart;
	
	private $logger;
	
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Magento\Quote\Model\QuoteRepository $quoteRepository,
		\Magento\Checkout\Model\Cart $cart,
		\Psr\Log\LoggerInterface $logger,
		Session $session
		
    ) {
		$this->logger = $logger;	
		$this->_session = $session;
		$this->_cart = $cart;
		$this->quoteRepository = $quoteRepository;
		$this->scopeConfig = $scopeConfig;
		$this->_productloader = $_productloader;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled(){
        return $this->scopeConfig->getValue('giftwrap/general/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
	
	public function isCheckoutShowSection(){
		if($this->isEnabled() == 1){
			$anyprodud = $this->isAnyProductsGiftCard();
			if($anyprodud){
				return '1';
			}
			return false;
		}
		return false;
	}
	
	public function isanyProductAllReadyInGiftWrap(){
		try{
			$quote = $this->_cart->getQuote();
			$quoteId = $quote->getId();
			$quote = $this->quoteRepository->get($quoteId);
			foreach ($quote->getItems() as $quoteItem){
				if($quoteItem->getIsGiftWrap() == 'yes'){
					return true;
				}
			}
			return false;
		}catch (\Exception $e) {
			$this->logger->critical($e->getMessage());
			return false;
		}
	}
	
	public function getSelectedOptions($item){
        $result = [];
        $options = $item->getProductOptions();
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }
        return $result;
    }
	
	public function getLoadProduct($id){
		try {
			return $this->_productloader->create()->load($id);
		} catch (\Exception $e) {
			$this->logger->critical($e->getMessage());
			return false;
		}
    }
	
	public function isProductsGiftCard($productid){
		try{
			$products = $this->getLoadProduct($productid);
			if($products):
				if($products->getIsGiftWrap() == 1){
					return true;
				}
				return false;
			endif;
			return false;
		} catch (\Exception $e) {
			$this->logger->critical($e->getMessage());
			return false;
		}	
	}
	
	public function isAnyProductsGiftCard(){
		try{
			$isgifts = null;
			$quote = $this->_cart->getQuote();
			$quoteId = $quote->getId();
			$quote = $this->quoteRepository->get($quoteId);
			foreach ($quote->getItems() as $quoteItem){
				if($isgifts == null){
					$products = $this->getLoadProduct($quoteItem->getProductId());
					if($products):
						if($products->getIsGiftWrap() == 1){
							return true;
						}
					endif;
				}
			}
			return false;
		}catch (\Exception $e) {
			$this->logger->critical($e->getMessage());
			return false;
		}
	}
	
}
