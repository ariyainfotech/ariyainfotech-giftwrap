define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Magento_Catalog/js/price-utils',
	'ko',
    'underscore'
], function ($, Component, quote, customer, priceUtils, ko, _) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'AriyaInfoTech_GiftWrap/checkout/payment/giftwrap',
        },
		canVisibleSection : window.checkoutConfig.giftwrap.isVisible,
		caProductListing : window.checkoutConfig.giftwrap.my_block_content,
		productSectionVisable: window.checkoutConfig.giftwrap.isDefautCheckoxSelectd,
		initObservable: function () {
            this._super()
                .observe({
                    isClickCheckox: ko.observable(window.checkoutConfig.giftwrap.isDefautCheckoxSelectd),
					isGiftWrapCheckox: ko.observable(false)
                });		
				
            this.isClickCheckox.subscribe(function (newValue) {
				console.log("hjhjhjh",window.checkoutConfig.giftwrap.isDefautCheckoxSelectd);
                if(newValue){
                    jQuery(".product-section").fadeIn();
                }else{
                    jQuery(".product-section").fadeOut();
                }
            });
			
			this.isGiftWrapCheckox.subscribe(function (itemSelected) {
				var itemId = jQuery(this).val();
                if(newValue){
                    console.log("Selected",itemId);
                }else{
                    console.log("IsUnSelected",itemId);
                }
            });
			
            return this;
        }
    });
});
