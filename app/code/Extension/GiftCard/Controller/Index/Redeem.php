<?php

namespace Extension\GiftCard\Controller\Index;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Extension\GiftCard\Helper\Data;
use Extension\GiftCard\Model\GiftCard;
use Extension\GiftCard\Model\History;

class Redeem extends Action
{
    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var GiftCard
     */
    protected GiftCard $giftCardFactory;

    /**
     * @var History
     */
    protected History $historyFactory;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param GiftCard $giftCardFactory
     * @param History $historyFactory
     * @param Data $helper
     */
    public function __construct(
        Context  $context,
        Session  $customerSession,
        GiftCard $giftCardFactory,
        History  $historyFactory,
        Data     $helper
    )
    {
        $this->customerSession = $customerSession;
        $this->giftCardFactory = $giftCardFactory;
        $this->historyFactory = $historyFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }


    /**
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $customer = $this->customerSession->getCustomer();

        // Retrieve form data from request
        $data = $this->getRequest()->getPostValue();

        $giftCardCode = $data['code'];

        // Check if the gift card code is valid
        $giftCard = $this->helper->validateGiftCardCode($giftCardCode);

        if ($giftCard) {
            // Get the available balance of the valid gift card
            $amountToRedeem = $this->helper->getAvailableGiftCardAmount($giftCard);

            if ($amountToRedeem > 0) {
                $currentBalance = $customer->getGiftcardBalance();

                $customer->setGiftcardBalance($currentBalance + $amountToRedeem);

                $this->helper->updateGiftCardBalance($giftCard, $amountToRedeem);

                // Add new record to the History
                $this->helper->updateHistory($giftCard->getId(), $amountToRedeem, 'redeem', $customer->getId());

                $customer->save();

                $this->messageManager->addSuccessMessage(__('Code redeemed successfully.'));
            } else {
                $this->messageManager->addErrorMessage(__('This Gift Card has no balance or has already been used.'));
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid gift card code.'));
        }

        // Redirect
        $this->_redirect('*/*/');
    }
}
