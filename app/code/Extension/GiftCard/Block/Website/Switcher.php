<?php

namespace Extension\GiftCard\Block\Website;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\TranslatedLists;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ResourceModel\Website\Collection as WebsiteCollection;
use Magento\Store\Model\ResourceModel\Website\CollectionFactory as WebsiteCollectionFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Switcher extends Template
{
    const LOCALE_CONFIG_PATH = 'general/locale/code';

    const DEFAULT_COUNTRY_CONFIG_PATH = 'general/country/default';

    private $websiteCollectionFactory;

    private $scopeConfig;

    private $translatedLists;

    private $storeManager;

    public function __construct(
        Context                  $context,
        StoreManagerInterface    $storeManager,
        WebsiteCollectionFactory $websiteCollectionFactory,
        ScopeConfigInterface     $scopeConfig,
        TranslatedLists          $translatedLists
    )
    {
        $this->websiteCollectionFactory = $websiteCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->translatedLists = $translatedLists;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getWebsite()
    {
        return $this->storeManager->getWebsite();
    }

    public function getWebsites(): WebsiteCollection
    {
        return $this->websiteCollectionFactory->create();
    }

    public function getStoreLocale(StoreInterface $store): string
    {
        $locale = $this->scopeConfig->getValue(self::LOCALE_CONFIG_PATH, ScopeInterface::SCOPE_STORE, $store->getId());

        return $locale;
    }

    public function getStoreCountryCode(StoreInterface $store): string
    {
        return $this->scopeConfig->getValue(
            self::DEFAULT_COUNTRY_CONFIG_PATH,
            ScopeInterface::SCOPE_STORE,
            $store->getId()
        );
    }
}
