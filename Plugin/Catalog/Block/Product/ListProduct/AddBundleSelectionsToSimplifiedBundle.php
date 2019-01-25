<?php

namespace MageSuite\Frontend\Plugin\Catalog\Block\Product\ListProduct;

class AddBundleSelectionsToSimplifiedBundle
{
    public function afterGetAddToCartPostParams($subject, $result, $product)
    {
        if ($product->getTypeId() == 'bundle' and $product->getIsSimplifiedBundle()) {
            $selectionCollection = $product->getTypeInstance()->getSelectionsCollection(
                $product->getTypeInstance()->getOptionsIds($product),
                $product
            );

            $bundleOptions = [];

            foreach ($selectionCollection as $selection) {
                $bundleOptions[$selection->getOptionId()][] = $selection->getSelectionId();
            }

            $result['data']['bundle_option'] = $bundleOptions;
        }

        return $result;
    }
}