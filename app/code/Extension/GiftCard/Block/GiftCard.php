<?php

namespace Extension\GiftCard\Block;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Extension\GiftCard\Api\GiftCardRepositoryInterface;
use Extension\GiftCard\Helper\Data;
use Extension\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory as GiftCardCollectionFactory;
use Extension\GiftCard\Model\ResourceModel\History\CollectionFactory as HistoryCollectionFactory;

class GiftCard extends Template
{
    /**
     * @var PricingHelper
     */
    protected PricingHelper $currency;
    /**
     * @var Session
     */
    protected Session $_customerSession;

    /**
     * @var Data
     */
    protected Data $dataHelper;

    /**
     * @var GiftCardRepositoryInterface
     */
    protected $giftCardRepository;

    /**
     * @var GiftCardCollectionFactory
     */
    protected $giftCardCollectionFactory;

    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    public function __construct(
        Context                     $context,
        PricingHelper               $currency,
        Session                     $customerSession,
        Data                        $dataHelper,
        GiftCardRepositoryInterface $giftCardRepository,
        GiftCardCollectionFactory   $giftCardCollectionFactory,
        HistoryCollectionFactory    $historyCollectionFactory
    )
    {
        $this->_customerSession = $customerSession;
        $this->currency = $currency;
        $this->dataHelper = $dataHelper;
        $this->giftCardRepository = $giftCardRepository;
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        parent::__construct($context);
    }

    /**
     * @return GiftCard
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Gift Card'));

        return parent::_prepareLayout();
    }

    /**
     * @return AbstractDb|AbstractCollection|null
     */
    public function getGiftCardCollection()
    {
        return $this->giftCardCollectionFactory->create();
    }

    /**
     * @return AbstractDb|AbstractCollection|null
     */
    public function getGiftCardHistory()
    {
        $historyCollection = $this->historyCollectionFactory->create();
        $customerId = $this->getCustomer()->getId();

        return $historyCollection->addFieldToFilter('customer_id', $customerId);
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * @param $id
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCodeById($id)
    {
        $giftCard = $this->giftCardRepository->getById($id);

        return $giftCard->getCode();
    }

    /**
     * @param $value
     * @return float|string
     */
    public function formatCurrency($value)
    {
        return $this->currency->currency($value);
    }

    /**
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getGiftCardBalance()
    {
        return $this->getCustomer()->getGiftcardBalance();
    }

    /**
     * @return mixed
     */
    public function isRedeemable()
    {
        return $this->dataHelper->getGiftCardConfig('allowredeem');
    }

    /**
     * @param $id
     * @return bool
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function testRepository($id)
    {
        return $this->giftCardRepository->deleteById($id);
    }
}
