<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Catalog\Model\Product $product */
$product = $objectManager->create(\Magento\Catalog\Model\Product::class);

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(555)
    ->setAttributeSetId(4)
    ->setName('First product')
    ->setSku('first_product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

/** @var \Magento\Catalog\Model\Product $product */
$product = $objectManager->create(\Magento\Catalog\Model\Product::class);

$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(556)
    ->setAttributeSetId(4)
    ->setName('Second product')
    ->setSku('second_product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->save();

$storeId = 1;

/** @var \Magento\Review\Model\Review $review */
$review = $objectManager->create(\Magento\Review\Model\Review::class)
    ->setEntityPkValue(555)
    ->setStatusId(\Magento\Review\Model\Review::STATUS_APPROVED)
    ->setTitle('Test title')
    ->setDetail('Test details')
    ->setEntityId(1)
    ->setStoreId($storeId)
    ->setStores([1])
    ->setCustomerId(null)
    ->setNickname('Test')
    ->save();

$ratingOptions = [
    '1' => '1',
    '2' => '2',
    '3' => '3'
];

foreach ($ratingOptions AS $ratingId => $optionIds) {
    /** @var \Magento\Review\Model\Rating $rating */
    $rating = $objectManager->create(\Magento\Review\Model\Rating::class);

    $rating->load($ratingId)
        ->setRatingCodes([$storeId => $rating->getRatingCode()])
        ->setStores([$storeId])
        ->save();

    /** @var \Magento\Review\Model\Rating $rating */
    $rating = $objectManager->create(\Magento\Review\Model\Rating::class);

    $rating->load($ratingId)
        ->setReviewId($review->getId())
        ->addOptionVote($optionIds, 555);
}

$review->aggregate();
