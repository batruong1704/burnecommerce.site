<?php

namespace Extension\GiftCard\Controller\Adminhtml\Code;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Extension\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory as GiftCardCollectionFactory;

class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var GiftCardCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param GiftCardCollectionFactory $collectionFactory
     */
    public function __construct(Context                                                            $context,
                                Filter                                                             $filter,
                                GiftCardCollectionFactory $collectionFactory
    )
    {
        parent::__construct($context, $filter);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return Redirect
     * @throws LocalizedException|Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $item) {
            $item->delete();
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $collectionSize));

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Extension_GiftCard::code');
    }
}
