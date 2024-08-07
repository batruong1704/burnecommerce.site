<?php

namespace Extension\GiftCard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class History extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('giftcard_history','history_id');
    }
}
