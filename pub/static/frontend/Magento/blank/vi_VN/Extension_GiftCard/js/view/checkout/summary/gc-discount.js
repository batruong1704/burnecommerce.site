define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/cart/totals-processor/default'
    ],
    function (ko,$,Component,quote,totals,priceUtils,defaultTotal) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Extension_GiftCard/checkout/summary/gc-discount'
            },
            totals: quote.getTotals(),
            shippingAddress: quote.shippingAddress(),
            giftCode: totals.getSegment('gc_discount'),

            isDisplayedGiftCardDiscountTotal : function () {
                return this.giftCode.value !== 0;
            },
            getGiftCardDiscountTotal : function () {
                var price = this.giftCode.value;
                this.updateSummary();
                console.log(price);
                return priceUtils.formatPrice(price, quote.getPriceFormat(), true);
            },
            updateSummary: function () {
                defaultTotal.estimateTotals(this.shippingAddress);
            }
        });
    }
);
