<?php

namespace Extension\GiftCard\Observer;

use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Extension\GiftCard\Helper\Data as DataHelper;
use Extension\GiftCard\Model\GiftCard;

class AfterGiftCodeApplied implements ObserverInterface
{
    /**
     * @var GiftCard
     */
    protected GiftCard $giftCardFactory;

    /**
     * @var DataHelper
     */
    protected DataHelper $helper;

    /**
     * @var MessageManagerInterface
     */
    protected MessageManagerInterface $messageManager;

    /**
     * @var CheckoutSession
     */
    protected CheckoutSession $checkoutSession;

    /**
     * @var RedirectInterface
     */
    protected RedirectInterface $redirect;

    /**
     * @var ActionFlag
     */
    protected ActionFlag $actionFlag;


    /**
     * @param GiftCard $giftCardFactory
     * @param DataHelper $helper
     * @param MessageManagerInterface $messageManager
     * @param CheckoutSession $checkoutSession
     * @param RedirectInterface $redirect
     * @param ActionFlag $actionFlag
     */
    public function __construct(GiftCard                $giftCardFactory,
                                DataHelper              $helper,
                                MessageManagerInterface $messageManager,
                                CheckoutSession         $checkoutSession,
                                RedirectInterface       $redirect,
                                ActionFlag              $actionFlag
    )
    {
        $this->giftCardFactory = $giftCardFactory;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->checkoutSession = $checkoutSession;
        $this->redirect = $redirect;
        $this->actionFlag = $actionFlag;
    }


    /**
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $redeemEnabled = $this->helper->getConfigValue(DataHelper::GIFTCARD_CHECKOUT_ENABLE);
        $quote = $this->checkoutSession->getQuote();
        $controller = $observer->getControllerAction();
        $couponCode = $controller->getRequest()->getParam('coupon_code');
        $isRemoveGiftCard = $controller->getRequest()->getParam('remove');

        if ($isRemoveGiftCard && $quote->getGiftCode() != null) {
            $quote->setGiftCode('');
            $quote->setGiftCodeDiscount(0);
            $quote->setTriggerRecollect(true);
            $quote->save();

            $this->messageManager->addSuccessMessage(__('Code removed successfully.'));
        } else {
            $giftCard = $this->helper->validateGiftCardCode($couponCode);

            if ($giftCard && $redeemEnabled) {
                // Get the available balance of the valid gift card
                $availableAmount = $this->helper->getAvailableGiftCardAmount($giftCard);
                if ($availableAmount > 0) {

                    $quote->setGiftCode($couponCode);
                    $quote->setGiftCodeDiscount($availableAmount);
                    $quote->setTriggerRecollect(true);
                    $quote->save();

                    $this->messageManager->addSuccessMessage(__('Code applied successfully.'));
                } else {
                    $this->messageManager->addErrorMessage(__('This Gift Card has no balance or has already been used.'));
                }

                $controller->getResponse()->setRedirect($this->redirect->getRefererUrl());
                $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
                $this->redirect->redirect($controller->getResponse(), $this->redirect->getRefererUrl());
            }
        }
    }
}
