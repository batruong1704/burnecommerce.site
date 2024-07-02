<?php

namespace Extension\GiftCard\Model\ResourceModel\GiftCard;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'giftcard_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Extension\GiftCard\Model\GiftCard::class, \Extension\GiftCard\Model\ResourceModel\GiftCard::class);
    }
}
