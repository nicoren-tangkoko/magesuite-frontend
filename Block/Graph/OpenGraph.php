<?php

namespace MageSuite\Frontend\Block\Graph;

use Magento\Framework\ObjectManagerInterface;


class OpenGraph extends \Magento\Framework\View\Element\Template
{
    protected $pageTitle;
    protected $storeInfo;
    protected $page;
    protected $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Page\Title $pageTitle,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Page $page,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->pageTitle = $pageTitle;
        $this->page = $page;

        parent::__construct($context, $data);
    }

    public function getStoreName()
    {
        return $this->_scopeConfig->getValue(
            'general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPageDescription()
    {
        return $this->page->getMetaDescription();
    }


    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }

    public function getTitle()
    {
        return $this->pageTitle->getShort();
    }

    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();

    }

    public function getProductDescription()
    {
        $description = '';
        $currentProduct = $this->getCurrentProduct();
        if ( $shortDescription = $currentProduct->getShortDescription() ) {
            $description = trim(strip_tags($shortDescription));
        } elseif ($longDescription = $currentProduct->getDescription()) {
            $description = trim(strip_tags(substr($longDescription, 0, 155)));
        }
        return $description;

    }

}
