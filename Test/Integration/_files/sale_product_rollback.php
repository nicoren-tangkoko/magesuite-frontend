<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$registry = $objectManager->get('Magento\Framework\Registry');
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productId = 634;
$product = $objectManager->create('Magento\Catalog\Model\Product');

$product->load($productId);
if ($product->getId()) {
    $product->delete();
}