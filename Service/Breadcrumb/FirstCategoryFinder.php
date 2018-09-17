<?php

namespace MageSuite\Frontend\Service\Breadcrumb;

class FirstCategoryFinder implements BreadcrumbCategoryFinderInterface
{
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(\Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Finds first category in product
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        $productCategories = $product->getAvailableInCategories();

        if(empty($productCategories) or !is_array($productCategories)) {
            return null;
        }

        $categoryId = $productCategories[0];

        return $this->categoryRepository->get($categoryId);
    }
}