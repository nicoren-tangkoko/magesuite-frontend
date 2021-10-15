<?php

namespace MageSuite\Frontend\ViewModel;

class FormatPrice implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    public function __construct(\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;
    }

    public function execute($price, $includeContainer = false, $precision = \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION, $scope = null, $currency = null) //phpcs:ignore
    {
        return $this->priceCurrency->convertAndFormat($price, $includeContainer, $precision, $scope, $currency);
    }
}
