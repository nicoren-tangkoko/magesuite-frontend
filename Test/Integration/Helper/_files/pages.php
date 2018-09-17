<?php
/** @var $store \Magento\Store\Model\Store */
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
if (!$store->load('second', 'code')->getId()) {
    $websiteId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Store\Model\StoreManagerInterface'
    )->getWebsite()->getId();
    $groupId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        'Magento\Store\Model\StoreManagerInterface'
    )->getWebsite()->getDefaultGroupId();
    $store->setCode(
        'second'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Second Store View'
    )->setSortOrder(
        10
    )->setIsActive(
        1
    );
    $store->save();
}

/* Refresh stores memory cache */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Store\Model\StoreManagerInterface'
)->reinitStores();

/** @var $page \Magento\Cms\Model\Page */
$page = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Cms\Model\Page');
$page->setTitle('Cms Page 1 Default Store View')
    ->setId(100)
    ->setIdentifier('site1-default')
    ->setStores([1])
    ->setIsActive(1)
    ->setContent('<h1>Cms Page 1 Default Store View</h1>')
    ->setPageLayout('1column')
    ->setPageGroupIdentifier('site1')
    ->save();

$page = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Cms\Model\Page');
$page->setTitle('Cms Page 1 Second Store View')
    ->setId(101)
    ->setIdentifier('site1-second')
    ->setStores([$store->getId()])
    ->setIsActive(1)
    ->setContent('<h1>Cms Page 1 Second Store View</h1>')
    ->setPageLayout('1column')
    ->setPageGroupIdentifier('site1')
    ->save();

$page->setTitle('Cms Page 2 All Store Views')
    ->setId(102)
    ->setIdentifier('site2-all')
    ->setStores([0])
    ->setIsActive(1)
    ->setContent('<h1>Cms Page 2 All Store Views</h1>')
    ->setPageLayout('1column')
    ->setPageGroupIdentifier('site2')
    ->save();
