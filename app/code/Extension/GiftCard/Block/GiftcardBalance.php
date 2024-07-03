<?php

namespace Extension\Giftcard\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session as CustomerSession;

class GiftcardBalance extends \Magento\Framework\View\Element\Template
{
    protected $customerSession;

    public function __construct(
        Template\Context $context,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getGiftcardBalance()
    {
        $customer = $this->customerSession->getCustomer();
        return $customer->getGiftcardBalance();
    }
}
