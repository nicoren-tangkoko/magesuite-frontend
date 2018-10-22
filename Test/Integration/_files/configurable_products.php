<?php
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;

/** @var ProductRepositoryInterface $productRepository */
$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);

$simpleProduct = $productRepository->get('simple_20');

$simpleProduct->setSpecialPrice(6.5);

$productRepository->save($simpleProduct);
