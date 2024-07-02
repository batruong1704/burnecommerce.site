<?php

namespace Extension\GiftCard\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface GiftCardSearchResultInterface extends SearchResultsInterface
{
    /**
     * @return \Extension\GiftCard\Api\Data\GiftCardInterface[]
     */
    public function getItems(): array;

    /**
     * @param \Extension\GiftCard\Api\Data\GiftCardInterface[] $items
     * @return void
     */
    public function setItems(array $items): void;
}
