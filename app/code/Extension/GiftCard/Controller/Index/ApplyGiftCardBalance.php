<?php

namespace Extension\GiftCard\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Response\RedirectInterface;

class ApplyGiftCardBalance extends Action
{
    protected CustomerSession $customerSession;
    protected RedirectInterface $redirect;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        RedirectInterface $redirect
    ) {
        $this->customerSession = $customerSession;
        $this->redirect = $redirect;
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($this->redirect->getRefererUrl());
    }
}
