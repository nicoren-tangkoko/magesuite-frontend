<?php

namespace MageSuite\Frontend\Helper;

class Checkout extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        parent::__construct($context);
        $this->scopeConfig = $scopeConfig;
    }

    public function getMinicartFlag()
    {
        return (boolean)$this->scopeConfig->getValue('checkout/cart/show_minicart', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}