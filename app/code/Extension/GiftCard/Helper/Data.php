<?php

namespace Extension\GiftCard\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\TestFramework\Exception\NoSuchActionException;
use Extension\GiftCard\Api\Data\GiftCardInterface;
use Extension\GiftCard\Model\GiftCard;
use Extension\GiftCard\Model\GiftCardFactory;
use Extension\GiftCard\Model\GiftCardRepository;
use Extension\GiftCard\Model\HistoryFactory;

class Data extends AbstractHelper
{
    const GIFTCARD_XML_PATH = 'giftcard/';
    const GIFTCARD_MODULE_ENABLE = 'giftcard/general/enable';

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;
    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var GiftCardRepository
     */
    protected $giftCardRepository;

    /**
     * @param Context $context
     * @param GiftCardFactory $giftCardFactory
     * @param HistoryFactory $historyFactory
     * @param GiftCardRepository $giftCardRepository
     */
    public function __construct(Context            $context,
                                GiftCardFactory    $giftCardFactory,
                                HistoryFactory     $historyFactory,
                                GiftCardRepository $giftCardRepository
    )
    {
        $this->giftCardFactory = $giftCardFactory;
        $this->historyFactory = $historyFactory;
        $this->giftCardRepository = $giftCardRepository;
        parent::__construct($context);
    }

    /**
     * @param $code
     * @param $storeId
     * @return mixed
     */
    public function getGiftCardConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::GIFTCARD_XML_PATH . 'general/' . $code, $storeId);
    }

    /**
     * @param $field
     * @param $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param GiftCardInterface $giftCard
     * @param $amountToUse
     * @return bool
     * @throws Exception
     */
    public function updateGiftCardBalance(GiftCardInterface $giftCard, $amountToUse)
    {
        $balance = $giftCard->getBalance();
        $amountUsed = $giftCard->getAmountUsed();
        if ($balance - $amountUsed < $amountToUse) {
            return false;
        } else {
            $giftCard->setAmountUsed($amountUsed + $amountToUse);
            $this->giftCardRepository->save($giftCard);

            return true;
        }
    }

    /**
     * @param $giftCardCode
     * @return GiftCard|null
     */
    public function validateGiftCardCode($giftCardCode)
    {
        // Return the gift card model if the code is valid, otherwise return null
        try {
            $giftCard = $this->giftCardRepository->getByCode($giftCardCode);
        }catch (Exception $exception)
        {
            return null;
        }
        return $giftCard;
    }

    /**
     * @param GiftCardInterface $giftCard
     * @return float|null
     */
    public function getAvailableGiftCardAmount(GiftCardInterface $giftCard)
    {
        $giftCardBalance = $giftCard->getBalance();
        $giftCardAmountUsed = $giftCard->getAmountUsed();

        return $giftCardBalance - $giftCardAmountUsed;
    }

    /**
     * @param $giftCardId
     * @param $amount
     * @param $action
     * @param $customerId
     * @return void
     * @throws Exception
     */
    public function updateHistory($giftCardId, $amount, $action, $customerId = null)
    {
        $newHistory = $this->historyFactory->create();
        $newHistory->setAmount($amount);
        $newHistory->setAction($action);
        $newHistory->setGiftcardId($giftCardId);
        $newHistory->setCustomerId($customerId);
        $newHistory->save();
    }


}
