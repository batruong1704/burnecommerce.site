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
    protected $customerRegistry;
    protected $timezone;

    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        AccountRedirect $accountRedirect,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        ScopeConfigInterface $scopeConfig,
        CustomerRegistry $customerRegistry,
        TimezoneInterface $timezone
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerAccountManagement,
            $accountRedirect,
            $customerHelperData,
            $formKeyValidator,
            $scopeConfig
        );
        $this->customerRegistry = $customerRegistry;
        $this->timezone = $timezone;
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
                    $customer = $this->customerRegistry->retrieveByEmail($login['username']);
                    $lockExpires = $this->getLockExpirationTime($customer);
                    $message = __('Tài khoản của bạn đã bị khóa tạm thời. Vui lòng thử lại sau.', $lockExpires);
                    $this->messageManager->addErrorMessage($message);

                } catch (AuthenticationException $e) {
                    $message = __('Thông tin đăng nhập không chính xác. Vui lòng kiểm tra lại email và mật khẩu.');
                    $this->messageManager->addErrorMessage($message);
                } catch (LocalizedException $e) {
                    if ($e->getMessage() == __('This account is locked.')) {
                        $message = __('Tài khoản của bạn đã bị khóa tạm thời. Vui lòng thử lại sau.');
                        $this->messageManager->addErrorMessage($message);
                    } else {
                        $this->messageManager->addErrorMessage($e->getMessage());
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Đã xảy ra lỗi không xác định. Vui lòng thử lại sau.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('Vui lòng nhập tên đăng nhập và mật khẩu.'));
            }
        }

        return $this->accountRedirect->getRedirect();
    }

    private function getLockExpirationTime($customer)
    {
        $lockExpires = $customer->getLockExpires();
        if ($lockExpires) {
            $now = $this->timezone->date()->getTimestamp();
            $lockExpiresTimestamp = strtotime($lockExpires);
            $diff = $lockExpiresTimestamp - $now;
            if ($diff > 0) {
                $minutes = ceil($diff / 60);
                return $minutes > 1 ? $minutes . ' minutes' : '1 minute';
            }
        }
        return 'a few minutes';
    }
}
