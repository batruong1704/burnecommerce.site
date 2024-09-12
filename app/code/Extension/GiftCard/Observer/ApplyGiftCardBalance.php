<?php

namespace Extension\GiftCard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Extension\GiftCard\Helper\Data as DataHelper;

class ApplyGiftCardBalance implements ObserverInterface
{
    protected DataHelper $helper;
    protected ManagerInterface $messageManager;
    protected CheckoutSession $checkoutSession;
    protected CustomerSession $customerSession;

    public function __construct(
        DataHelper $helper,
        ManagerInterface $messageManager,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession
    ) {
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
    }

    public function execute(Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $amountToApply = (float) $controller->getRequest()->getParam('giftcard_amount');
        $customer = $this->customerSession->getCustomer();
        $currentGiftcardBalance = (float) $this->customerSession->getCustomer()->getGiftcardBalance();

        if ($amountToApply > 0 && $amountToApply <= $currentGiftcardBalance) {
            // Reduce grand total by gift card balance
            $quote = $this->checkoutSession->getQuote();
            $baseGrandTotal = $quote->getBaseGrandTotal();
            $grandTotal = $quote->getGrandTotal();

            $newBaseGrandTotal = max(0, $baseGrandTotal - $amountToApply);
            $newGrandTotal = max(0, $grandTotal - $amountToApply);

            $quote->setBaseGrandTotal($newBaseGrandTotal);
            $quote->setGrandTotal($newGrandTotal);

            // Apply discount to quote
            $discountAmount = $quote->getBaseSubtotal() - $newBaseGrandTotal;
            $quote->setSubtotalWithDiscount($quote->getSubtotal() - $discountAmount);
            $quote->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount);

            // Update customer gift card balance
            $newBalance = max(0, $currentGiftcardBalance - $amountToApply);
            $this->customerSession->getCustomer()->setGiftcardBalance($newBalance);
            $this->customerSession->getCustomer()->save();

            // Record history
            $this->helper->updateHistory("5", $amountToApply, 'check to cart', $customer->getId());

            // Add success message to customer
            $this->messageManager->addSuccessMessage(__('Gift Card balance applied successfully.'));
            // Add notice about non-refundable
            $this->messageManager->addNoticeMessage(__('Note: Gift Card balance once applied cannot be refunded.'));

            // Update quote totals and save
            $quote->setSubtotal($quote->getSubtotal() - $amountToApply);
            $quote->setBaseSubtotal($quote->getBaseSubtotal() - $amountToApply);
            $quote->collectTotals()->save();
        } else {
            $this->messageManager->addErrorMessage(__('Invalid amount or insufficient Gift Card balance.'));
        }
    }
}
