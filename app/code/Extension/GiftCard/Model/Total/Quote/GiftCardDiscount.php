<?php

namespace Extension\GiftCard\Model\Total\Quote;

use Exception;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Extension\GiftCard\Helper\Data as DataHelper;
use Extension\GiftCard\Model\GiftCardRepository;

class GiftCardDiscount extends AbstractTotal
{
    /**
     * @var GiftCardRepository
     */
    protected GiftCardRepository $giftCardRepository;

    /**
     * @var CartRepositoryInterface
     */
    protected CartRepositoryInterface $quoteRepository;

    /**
     * @var DataHelper
     */
    protected DataHelper $helper;

    /**
     * @var PriceCurrencyInterface
     */
    protected PriceCurrencyInterface $_priceCurrency;

    /**
     * @var MessageManagerInterface
     */
    protected MessageManagerInterface $messageManager;

    /**
     *
     * @param PriceCurrencyInterface $priceCurrency
     * @param GiftCardRepository $giftCardRepository
     * @param DataHelper $helper
     * @param MessageManagerInterface $messageManager
     * @param CartRepositoryInterface $cartRepositoryInterface
     */
    public function __construct(
        PriceCurrencyInterface  $priceCurrency,
        GiftCardRepository      $giftCardRepository,
        DataHelper              $helper,
        MessageManagerInterface $messageManager,
        CartRepositoryInterface $cartRepositoryInterface
    )
    {
        $this->_priceCurrency = $priceCurrency;
        $this->giftCardRepository = $giftCardRepository;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->quoteRepository = $cartRepositoryInterface;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     * @throws Exception
     */
    public function collect(
        Quote                       $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total                       $total
    )
    {
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        parent::collect($quote, $shippingAssignment, $total);

        $appliedGiftCode = $quote->getGiftCode();
        $giftCard = $this->helper->validateGiftCardCode($appliedGiftCode);

        if ($appliedGiftCode) {

            if (!$giftCard || $this->helper->getAvailableGiftCardAmount($giftCard) <= 0) {
                $quote->setGiftCode('');
                $quote->setGiftCodeDiscount(0);
                $quote->setTriggerRecollect(true);
                $this->quoteRepository->save($quote);

                $this->messageManager->addErrorMessage(__('Applied Gift Card is no longer available, please reload the page'));
            } else {
                $amountToDiscount = $this->getDiscountAmount($quote);
                $discount = $this->_priceCurrency->convert(-$amountToDiscount);

                $total->addTotalAmount('gc_discount', $discount);
                $total->addBaseTotalAmount('gc_discount', $discount);
//                $quote->setCustomDiscount(-$discount);
            }
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        $amountToDiscount = $this->getDiscountAmount($quote);

        return [
            'code' => 'gc_discount',
            'title' => $this->getLabel(),
            'value' => -$amountToDiscount
        ];
    }

    /**
     * @return Phrase
     */
    public function getLabel()
    {
        return __('Gift Card');
    }

    /**
     * @param Quote $quote
     * @return int|mixed
     */
    public function getDiscountAmount(Quote $quote)
    {
        $giftCode = $quote->getGiftCode();
        $giftCard = $this->helper->validateGiftCardCode($giftCode);

        if ($giftCard) {
            $availableAmount = $this->helper->getAvailableGiftCardAmount($giftCard);

            if ($availableAmount != $quote->getGiftCodeDiscount()) {
                $quote->setGiftCodeDiscount($availableAmount);
                $this->quoteRepository->save($quote);
            }

            return min($availableAmount, $quote->getSubtotalWithDiscount());
        }

        return 0;
    }
}
