<?php

namespace Extension\GiftCard\Model;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Extension\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $giftCardCollectionFactory
     * @param array $meta
     * @param array $data
     */
    // @codingStandardsIgnoreEnd
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $giftCardCollectionFactory,
        array $meta = [],
        array $data = []
    )
    {
        $this->collection = $giftCardCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    // @codingStandardsIgnoreEnd
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $giftCard) {
            $this->loadedData[$giftCard->getData('giftcard_id')]['giftcard'] = $giftCard->getData();
        }

        return $this->loadedData;
    }
}
