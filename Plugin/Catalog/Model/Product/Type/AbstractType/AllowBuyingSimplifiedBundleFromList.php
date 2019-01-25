<?php

namespace MageSuite\Frontend\Plugin\Catalog\Model\Product\Type\AbstractType;

class AllowBuyingSimplifiedBundleFromList
{
    public function afterIsPossibleBuyFromList(\Magento\Catalog\Model\Product\Type\AbstractType $subject,  $result, $product) {
        if(!is_object($product)) {
            return $result;
        }

        if($product->getTypeId() == 'bundle' and $product->getIsSimplifiedBundle()) {
            return true;
        }

        return $result;
    }
}