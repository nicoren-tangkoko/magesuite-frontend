<?php

namespace MageSuite\Frontend\Model\Sitemap;

class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * @inheritDoc
     */
    protected function _getUrl($url, $type = \Magento\Framework\UrlInterface::URL_TYPE_LINK)
    {
        if (stripos($url, 'http') === 0) {
            return ltrim($url, '/');
        }

        return $this->_getStoreBaseUrl($type) . ltrim($url, '/');
    }
}
