<?php

namespace Extension\GiftCard\Block;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Eav\Api\Data\AttributeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\Form\AttributeMapper;

class LayoutProcessor implements LayoutProcessorInterface
{

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @param AttributeMerger $merger
     * @param AttributeMapper $attributeMapper
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     */
    public function __construct(
        AttributeMerger               $merger,
        AttributeMapper               $attributeMapper,
        AttributeMetadataDataProvider $attributeMetadataDataProvider
    )
    {
        $this->merger = $merger;
        $this->attributeMapper = $attributeMapper;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
    }

    /**
     * @param $jsLayout
     * @return array
     * @throws LocalizedException
     */
    public function process($jsLayout)
    {
        $elements = $this->getAddressAttributes();

        $fields = &$jsLayout['components']['checkout']['children']['steps']['children']['contact-step']['children']['contact']['children']['contact-fieldset']['children'];

        $fieldCodes = array_keys($fields);

        $elements = array_filter($elements, function ($key) use ($fieldCodes) {
            return in_array($key, $fieldCodes);
        }, ARRAY_FILTER_USE_KEY);

        $fields = $this->merger->merge(
            $elements,
            'checkoutProvider',
            'contact',
            $fields
        );

        return $jsLayout;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    private function getAddressAttributes(): array
    {
        /** @var AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];

        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }

        return $elements;
    }
}
