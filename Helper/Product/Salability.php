<?php

namespace MageSuite\Frontend\Helper\Product;

class Salability extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Stock
     */
    private $stockHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \MageSuite\Frontend\Helper\Product\Stock $stockHelper
    ) {
        parent::__construct($context);
        $this->stockHelper = $stockHelper;
    }

    /**
     * This function allows getting salability status for multiple products using only one SQL query
     */
    public function getSalabilityStatus($productCollection) {
        $return = [];

        $configurableProducts = [];

        /** @var \Magento\Catalog\Model\Product $product */
        foreach($productCollection as $product) {
            if($product->getTypeId() == 'configurable') {
                $return[$product->getId()] = false;
                $configurableProducts[] = $product;
            } else {
                $return[$product->getId()] = $product->getIsSalable();
            }
        }

        if (empty($configurableProducts)) {
            return $return;
        }

        $usedSimpleProductsIds = [];

        foreach($configurableProducts as $configurableProduct) {
            $usedSimpleProductsIds = array_merge($usedSimpleProductsIds, $configurableProduct->getTypeInstance()->getUsedProductIds($configurableProduct));
        }

        $stockStatuses = $this->stockHelper->getStockStatuses($usedSimpleProductsIds);

        foreach($configurableProducts as $configurableProduct) {
            foreach($configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct) as $simpleProduct) {
                $stockStatus = $stockStatuses[$simpleProduct->getId()];

                if((int)$stockStatus->getStockStatus() === \Magento\CatalogInventory\Model\Stock\Status::STATUS_IN_STOCK and $simpleProduct->isSalable()) {
                    $return[$configurableProduct->getId()] = true;
                    break;
                }
            }
        }

        return $return;
    }


}
