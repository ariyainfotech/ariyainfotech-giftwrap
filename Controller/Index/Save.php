<?php
/**
 * Copyright © Magento All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AriyaInfoTech\GiftWrap\Controller\Index;

class Save extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
	
    protected $jsonHelper;
	
	protected $_cart;  
	
	protected $quoteRepository;  

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
		\Magento\Quote\Model\QuoteRepository $quoteRepository,
		\Magento\Checkout\Model\Cart $cart,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
		$this->_cart = $cart;
		$this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
			
            $isgiftwrap = $this->getRequest()->getParam('isgiftwrap');
			$itemId = $this->getRequest()->getParam('itemid');
			$message = $this->getRequest()->getParam('messageadd');
			
			$quote = $this->_cart->getQuote();
			$quoteId = $quote->getId();
			$quote = $this->quoteRepository->get($quoteId);
			foreach ($quote->getItems() as $quoteItem) {
				if($quoteItem->getId() == $itemId){
					if($isgiftwrap == 'yes'){
						$quoteItem->setIsGiftWrap($isgiftwrap);
					}elseif($isgiftwrap == 'no'){
						$quoteItem->setIsGiftWrap("");
					}
					if($message == 'no'){
						$quoteItem->setGiftWrapMessage("");	
					}else{
						$quoteItem->setGiftWrapMessage($message);
					}
				}
			}
			$quote->save();
			return $this->jsonResponse("true");
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}