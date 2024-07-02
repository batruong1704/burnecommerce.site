<?php

namespace Extension\GiftCard\Controller\Adminhtml\Code;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;

class NewAction extends Action
{
    /**
     * @return ResponseInterface|ResultInterface|Page|(Page&ResultInterface)
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__('New GiftCard Code')));

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
