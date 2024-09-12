<?php

namespace Extension\Giftcard\Plugin\Quote;

use Magento\Quote\Model\Quote;
use Magento\Customer\Model\Session as CustomerSession;

class ApplyGiftCard
{

    protected $customerSession;

    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function beforeCollectTotals(Quote $quote)
    {
        $customer = $this->customerSession->getCustomer();
        $giftCardBalance = $customer->getGiftcardBalance();

        if ($giftCardBalance > 0) {
            $total = $quote->getBaseSubtotal();
            $appliedBalance = min($giftCardBalance, $total);

            $quote->setGiftCardDiscount($appliedBalance);
            $quote->setBaseGiftCardDiscount($appliedBalance);

            $quote->setSubtotal($quote->getSubtotal() - $appliedBalance);
            $quote->setBaseSubtotal($quote->getBaseSubtotal() - $appliedBalance);

            $quote->setGrandTotal($quote->getGrandTotal() - $appliedBalance);
            $quote->setBaseGrandTotal($quote->getBaseGrandTotal() - $appliedBalance);

            $customer->setGiftcardBalance($giftCardBalance - $appliedBalance);
            $customer->save();
        }
    }
}
