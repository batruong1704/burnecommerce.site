<?php

namespace Extension\GiftCard\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface GiftCardInterface extends ExtensibleDataInterface
{
    const ID = 'giftcard_id';
    const CODE = 'code';
    const BALANCE = 'balance';
    const AMOUNT_USED = 'amount_used';
    const
        CREATED_FROM = 'created_from';
    const CREATED_AT = 'created_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return void
     */
    public function setCode($code);

    /**
     * @return float | null
     */
    public function getBalance();

    /**
     * @param float $balance
     * @return void
     */
    public function setBalance($balance);

    /**
     * @return float | null
     */
    public function getAmountUsed();

    /**
     * @param float $amountUsed
     * @return void
     */
    public function setAmountUsed($amountUsed);

    /**
     * @return string
     */
    public function getCreatedFrom();

    /**
     * @param string $from
     * @return void
     */
    public function setCreatedFrom($from);

//    /**
//     * @return \Extension\GiftCard\Api\Data\GiftCardExtensionInterface|null
//     */
//    public function getExtensionAttributes();
//
//    /**
//     * @param \Extension\GiftCard\Api\Data\GiftCardExtensionInterface $extensionAttributes
//     * @return void
//     */
//    public function setExtensionAttributes(GiftCardExtensionInterface $extensionAttributes);
}
