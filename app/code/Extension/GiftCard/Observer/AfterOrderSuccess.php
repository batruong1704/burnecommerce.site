<?php

namespace Extension\GiftCard\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Extension\GiftCard\Helper\Data as DataHelper;
use Extension\GiftCard\Model\GiftCard;

class AfterOrderSuccess implements ObserverInterface
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
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    /**
     * @param GiftCard $giftCard
     * @param DataHelper $helper
     * @param CartRepositoryInterface $quoteRepository
     */
    public function __construct(GiftCard                $giftCard,
                                DataHelper              $helper,
                                CartRepositoryInterface $quoteRepository,
    )
    {
        $this->helper = $helper;
        $this->giftCardFactory = $giftCard;
        $this->quoteRepository = $quoteRepository;
    }


    /**
     * @param Observer $observer
     * @return void
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteRepository->get($quoteId);
        $ci = $order->getCustomerId();

        $giftCode = $quote->getGiftCode();
        $giftCard = $this->helper->validateGiftCardCode($giftCode);

        if ($giftCard) {
            $availableAmount = $this->helper->getAvailableGiftCardAmount($giftCard);
            $amountToBeUsed = min($availableAmount, $quote->getSubtotal()) ?? 0;

            $this->helper->updateGiftCardBalance($giftCard, $amountToBeUsed);
            $this->helper->updateHistory($giftCard->getId(), $amountToBeUsed, 'used for order', $ci);
        }
    }
}
