<?php

namespace MageSuite\Frontend\Block\Category\View;

class Headline extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\View\Page\Title
     */
    private $pageTitle;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    private $layerResolver;
    /**
     * @var \MageSuite\Frontend\Helper\Category
     */
    private $categoryHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\View\Page\Title $pageTitle,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry,
        \MageSuite\Frontend\Helper\Category $categoryHelper,
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->pageTitle = $pageTitle;
        $this->layerResolver = $layerResolver;
        $this->categoryHelper = $categoryHelper;

        parent::__construct($context, $data);
    }

    public function getIcon() {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->registry->registry('current_category');

        if (empty($category)) {
            return null;
        }
        
        return $this->categoryHelper->getCategoryIcon($category);
    }
    
    public function getHeadline()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->registry->registry('current_category');

        if (empty($category)) {
            return $this->pageTitle->getShort();
        } else {
            return $category->getName();
        }
    }

    public function getCollectionSize()
    {
        $layer = $this->layerResolver->get();
        $collection = $layer->getProductCollection();
        return $collection->getSize();
    }


}