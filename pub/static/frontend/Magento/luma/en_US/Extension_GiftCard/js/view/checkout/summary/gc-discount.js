define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/action/get-totals'
    ],
    function ($,Component,quote,totals,priceUtils,getTotalsAction) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Extension_GiftCard/checkout/summary/gc-discount'
            },
            totals: quote.getTotals(),
            giftCode: totals.getSegment('gc_discount'),

            isDisplayedGiftCardDiscountTotal : function () {
                return this.giftCode.value !== 0;
            },
            getGiftCardDiscountTotal : function () {
                // this.updateSummary();
                var price = this.giftCode.value;
                console.log(price);
                return priceUtils.formatPrice(price, quote.getPriceFormat(), true);
            },
            updateSummary: function () {
                var deferred = $.Deferred();
                getTotalsAction([], deferred);
            }
        });
    }
);
