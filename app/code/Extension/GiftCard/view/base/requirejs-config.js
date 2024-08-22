var config = {
    'config': {
        'mixins': {
            'Magento_Checkout/js/view/shipping': {
                'Extension_GiftCard/js/view/checkout/shipping-payment-mixin': true
            },
            'Magento_Checkout/js/view/payment': {
                'Extension_GiftCard/js/view/checkout/shipping-payment-mixin': true
            }
        }
    }
}
