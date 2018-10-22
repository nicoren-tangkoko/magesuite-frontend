<?php
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);

$bundleProduct = $productRepository->get('bundle-product');

$bundleProduct->setSpecialPrice(65);

$productRepository->save($bundleProduct);
