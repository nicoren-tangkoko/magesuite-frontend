<?php

namespace MageSuite\Frontend\Block\Product\View;

use Magento\Catalog\Model\Product;

class Price extends \Magento\Catalog\Block\Product\View\Price
{
    /**
     * @var Product
     */
    protected $product = null;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $pricingHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        $this->pricingHelper = $pricingHelper;
        parent::__construct($context, $registry, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if (!$this->getProduct()->getData($this->getPriceTypeCode())) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->_coreRegistry->registry('product');
        }

        return $this->product;
    }

    public function getFormattedPrice()
    {
        $price = $this->getProduct()->getData($this->getPriceTypeCode());
        $formattedPrice = $this->pricingHelper->currency($price, true, false);
        return $formattedPrice;
    }
}
