<?php

namespace Extension\GiftCard\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Extension\GiftCard\Api\Data\GiftCardInterface;


class GiftCard extends AbstractExtensibleModel implements GiftCardInterface
{
    /**
     * @inheritDoc
     */
    public function getCode()
    {
        return $this->_getData(self::CODE);
    }

    /**
     * @inheritDoc
     */
    public function setCode($code)
    {
        $this->setData(self::CODE, $code);
    }

    /**
     * @inheritDoc
     */
    public function getBalance()
    {
        return $this->_getData(self::BALANCE);
    }

    /**
     * @inheritDoc
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * @inheritDoc
     */
    public function getAmountUsed()
    {
        return $this->_getData(self::AMOUNT_USED);
    }

    /**
     * @inheritDoc
     */
    public function setAmountUsed($amountUsed)
    {
        return $this->setData(self::AMOUNT_USED, $amountUsed);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedFrom()
    {
        return $this->_getData(self::CREATED_FROM);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedFrom($from)
    {
        return $this->setData(self::CREATED_FROM, $from);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\GiftCard::class);
        $this->setIdFieldName('giftcard_id');
    }
}
