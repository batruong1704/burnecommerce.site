<?php

namespace Extension\GiftCard\Controller\Index;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Extension\GiftCard\Helper\Data;

class Index implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected RedirectFactory $redirectFactory;

    /**
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     * @param CustomerRepositoryInterface $customerRepository
     * @param RedirectFactory $redirectFactory
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        Session                     $customerSession,
        PageFactory                 $resultPageFactory,
        Data                        $helper,
        CustomerRepositoryInterface $customerRepository,
        RedirectFactory             $redirectFactory,
        MessageManagerInterface     $messageManager
    )
    {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page
     */
    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        if ($this->customerSession->isLoggedIn()) {
            if ($this->helper->getConfigValue(Data::GIFTCARD_MODULE_ENABLE)) {
                return $this->resultPageFactory->create();
            } else {
                $this->messageManager->addNoticeMessage('This feature is not available at the moment');
                return $resultRedirect->setPath('customer/account/');
            }
        } else {
            $this->messageManager->addErrorMessage('Please login to view this page');
            return $resultRedirect->setPath('customer/account/login');
        }
    }
}
