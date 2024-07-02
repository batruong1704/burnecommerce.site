<?php

namespace Extension\GiftCard\Model;

use Magento\Framework\Model\AbstractModel;

class History extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(ResourceModel\History::class);
    }
}
