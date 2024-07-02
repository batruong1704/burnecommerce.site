<?php

namespace Extension\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Setup\Exception;
use Extension\GiftCard\Api\GiftCardRepositoryInterface;
use Extension\GiftCard\Helper\CodeGenerator;
use Extension\GiftCard\Model\GiftCardFactory;


class Duplicate extends Action
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
     * @var CodeGenerator
     */
    protected $codeGenerator;

    public function __construct(Context                     $context,
                                GiftCardFactory             $giftCardFactory,
                                GiftCardRepositoryInterface $giftCardRepository,
                                CodeGenerator               $codeGenerator
    )
    {
        $this->giftcardFactory = $giftCardFactory;
        $this->giftCardRepository = $giftCardRepository;
        $this->codeGenerator = $codeGenerator;

        parent::__construct($context);
    }


    /**
     * @return ResponseInterface|Redirect|(Redirect&ResultInterface)|ResultInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $giftCardToDuplicate = $this->giftCardRepository->getById($id);

        $newCode = $this->codeGenerator->generateGiftCode(strlen($giftCardToDuplicate->getCode()));

        $newGiftCard = $this->giftcardFactory->create();
        $newGiftCard->setData($giftCardToDuplicate->getData());
        $newGiftCard->setCode($newCode);

        try {
            $this->giftCardRepository->save($newGiftCard);
            $this->messageManager->addSuccessMessage(__('Gift card has been duplicated.'));
        } catch (Exception $ex) {
            $this->messageManager->addErrorMessage(__('Error occurred while duplicating the Gift card.'));
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Extension_GiftCard::code');
    }
}
