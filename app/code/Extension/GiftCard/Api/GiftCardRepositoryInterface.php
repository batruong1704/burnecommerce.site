<?php

declare(strict_types=1);

namespace Extension\GiftCard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Extension\GiftCard\Api\Data\GiftCardInterface;

interface GiftCardRepositoryInterface
{
    /**
     * @param int $id
     * @return \Extension\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id): GiftCardInterface;

    /**
     * @param string $code
     * @return \Extension\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCode($code): GiftCardInterface;

    /**
     * @param \Extension\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return \Extension\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(GiftCardInterface $giftCard): GiftCardInterface;

    /**
     * @param \Extension\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(GiftCardInterface $giftCard): bool;

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id): bool;

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Extension\GiftCard\Api\Data\GiftCardSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
