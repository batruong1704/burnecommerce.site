<?php

namespace Extension\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page;
use Extension\GiftCard\Api\GiftCardRepositoryInterface;
use Extension\GiftCard\Model\GiftCardFactory;


class Edit extends Action
{
    /**
     * @var GiftCardFactory
     */
    protected $giftcardFactory;

    /**
     * @var GiftCardRepositoryInterface
     */
    protected $giftCardRepository;


    /**
     * @param Context $context
     * @param GiftCardFactory $giftCardFactory
     * @param GiftCardRepositoryInterface $giftCardRepository
     */
    public function __construct(Context                     $context,
                                GiftCardFactory             $giftCardFactory,
                                GiftCardRepositoryInterface $giftCardRepository
    )
    {
        $this->giftcardFactory = $giftCardFactory;
        $this->giftCardRepository = $giftCardRepository;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page|(Page&ResultInterface)
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $giftCard = $this->giftCardRepository->getById($id);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__('Edit ' . $giftCard->getCode())));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Extension_GiftCard::code');
    }
}
