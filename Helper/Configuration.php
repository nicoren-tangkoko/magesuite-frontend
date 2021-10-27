<?php

namespace MageSuite\Frontend\Helper;

class Configuration
{
    const XML_PATH_ASSETS_URL_TIMESTAMP = 'web/assets/url_timestamp';

    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    protected $configCollectionFactory;

    public function __construct(\Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory)
    {
        $this->configCollectionFactory = $configCollectionFactory;
    }

    public function getAssetsUrlTimestamp() {
        return $this->getUncachedConfigValue(self::XML_PATH_ASSETS_URL_TIMESTAMP);
    }

    protected function getUncachedConfigValue($path): ?string {
        $configCollection = $this->configCollectionFactory->create();
        $configCollection->addFieldToFilter('path', ['eq' => $path]);

        $config =  $configCollection->getFirstItem();

        if($config === null) {
            return '';
        }

        return $config->getValue() ?? '';
    }
}
