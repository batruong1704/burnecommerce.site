<?php

namespace Extension\GiftCard\Model\ResourceModel\History;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'history_id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Extension\GiftCard\Model\History::class, \Extension\GiftCard\Model\ResourceModel\History::class);
    }
}
