<?php

namespace Extension\GiftCard\Ui\Component\Field;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

class ConfigurableDefaultValue extends Field
{
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ScopeConfigInterface $scopeConfig,
        array $components = [],
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;

        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    public function getConfiguration(): array
    {
        $config = parent::getConfiguration();
        $config['default'] = $this->scopeConfig->getValue($config['defaultValueConfigPath']);

        return $config;
    }
}
