<?php
namespace Extension\GiftCard\Controller\Account;

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\Phrase;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Post login customer action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends \Magento\Customer\Controller\Account\LoginPost
{
    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerUrl
     * @param Validator $formKeyValidator
     * @param AccountRedirect $accountRedirect
     * @param CustomerRegistry $customerRegistry
     * @param TimezoneInterface $timezone
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerUrl,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect,
        CustomerRegistry $customerRegistry,
        TimezoneInterface $timezone
    ) {
        $this->customerRegistry = $customerRegistry;
        $this->timezone = $timezone;
        parent::__construct(
            $context,
            $customerSession,
            $customerAccountManagement,
            $customerUrl,
            $formKeyValidator,
            $accountRedirect
        );
    }

    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('*/*/');
                    return $resultRedirect;
                } catch (EmailNotConfirmedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (UserLockedException $e) {
                    try {
                        $customer = $this->customerRegistry->retrieveByEmail($login['username']);
                        $lockExpires = $customer->getLockExpires();
                        if ($lockExpires) {
                            $now = $this->timezone->date()->getTimestamp();
                            $lockExpiresTimestamp = strtotime($lockExpires);
                            $diff = $lockExpiresTimestamp - $now;
                            $hours = floor($diff / 3600);
                            $minutes = floor(($diff % 3600) / 60);
                            if($hours == 0) {
                                $timeRemaining = sprintf('%d minutes', $minutes);
                            } else {
                                $timeRemaining = sprintf('%d hours %d minutes', $hours, $minutes);
                            }
                            $message = __('Your account is locked. Please try again after %1.', $timeRemaining);
                        } else {
                            $message = __('Your account has been locked. Please try again later!');
                        }
                    } catch (\Exception $e) {
                        $message = __('Your account has been locked. Please try again later!');
                    }
                    $this->messageManager->addErrorMessage($message);
                } catch (AuthenticationException $e) {
                    $message = __('Incorrect email or password, please try again. Note that more than 5 incorrect attempts will result in your account being locked!');
                    $this->messageManager->addErrorMessage($message);
                } catch (LocalizedException $e) {
                    if ($e->getMessage() == __('This account is locked.')) {
                        $message = __('Your account is locked. Please try again later!');
                        $this->messageManager->addErrorMessage($message);
                    } else {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('An unknown error occurred. Please try again later!'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('Please enter email and password!'));
            }
        }

        return $this->accountRedirect->getRedirect();
    }
}
