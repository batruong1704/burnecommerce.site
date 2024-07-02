<?php

namespace Extension\GiftCard\Controller\Adminhtml\Code;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Extension\GiftCard\Api\GiftCardRepositoryInterface;
use Extension\GiftCard\Helper\CodeGenerator;
use Extension\GiftCard\Model\GiftCardFactory;

class Save extends Action
{
    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var GiftCardRepositoryInterface
     */
    protected $giftCardRepository;

    /**
     * @var CodeGenerator
     */
    protected $codeGenerator;

    /**
     * @param Context $context
     * @param GiftCardFactory $giftCardFactory
     * @param CodeGenerator $codeGenerator
     * @param GiftCardRepositoryInterface $giftCardRepository
     */
    public function __construct(Context                     $context,
                                GiftCardFactory             $giftCardFactory,
                                CodeGenerator               $codeGenerator,
                                GiftCardRepositoryInterface $giftCardRepository
    )
    {
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardRepository = $giftCardRepository;
        $this->codeGenerator = $codeGenerator;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $giftCard = $this->giftCardFactory->create();
        $postData = $this->getRequest()->getPostValue();
        $formData = $postData['giftcard'];

        $resultRedirect = $this->resultRedirectFactory->create();

        if (!$postData) {
            $this->$resultRedirect->setPath('*/*/');
        }

        if (array_key_exists('giftcard_id', $formData)) {
            $id = $formData['giftcard_id'];
            $giftCard = $this->giftCardRepository->getById($id);
            $giftCard->setData($formData);
        } else {
            // Get code_length
            $code_length = $formData["code_length"];
            // Get balance
            $balance = $formData["balance"];

            if ($code_length <= 0) {
                throw new Exception('Invalid code length.');
            }

            // Generate Gift Code
            $giftCode = $this->codeGenerator->generateGiftCode($code_length);

            $giftCard->setData(['code' => $giftCode, 'balance' => $balance]);
        }
        try {
            $this->giftCardRepository->save($giftCard);
            $this->messageManager->addSuccessMessage(__('GiftCard saved successfully.'));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while saving the GiftCard.'));
        }

        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit/id/' . $giftCard->getId());
        }

        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Extension_GiftCard::code');
    }

}
