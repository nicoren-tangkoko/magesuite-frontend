<?php

namespace MageSuite\Frontend\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MINIMAL_SALE_PERCENTAGE_PATH = 'catalog/frontend/minimal_sale_percentage';
    const DISPLAY_DISCOUNT_BADGES_PER_PRODUCT = 'catalog/frontend/display_discount_badges_per_product';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfigInterface;
    }

    public function getMinimalSalePercentage()
    {
        return $this->scopeConfig->getValue(self::MINIMAL_SALE_PERCENTAGE_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getDisplayDiscountBadgesPerProduct()
    {
        return $this->scopeConfig->getValue(self::DISPLAY_DISCOUNT_BADGES_PER_PRODUCT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
