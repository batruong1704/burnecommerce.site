<?php

namespace Extension\GiftCard\Plugin;

use Magento\Checkout\Block\Cart\Coupon;
use Magento\Checkout\Model\Session as CheckoutSession;

class CheckGiftCardApplied
{

    /**
     * @var CheckoutSession
     */
    protected CheckoutSession $_checkoutSession;

    public function __construct(CheckoutSession $_checkoutSession)
    {
        $this->_checkoutSession = $_checkoutSession;
    }

    public function afterGetCouponCode(Coupon $subject, $result)
    {
        $quote = $this->_checkoutSession->getQuote();

        $giftCode = $quote->getGiftCode();

        if ($giftCode !== null && strlen($giftCode) > 0) {
            $result = $giftCode;
        }

        return $result;
    }
}
