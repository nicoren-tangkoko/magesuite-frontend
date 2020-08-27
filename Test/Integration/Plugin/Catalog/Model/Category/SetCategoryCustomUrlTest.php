<?php

namespace MageSuite\Frontend\Test\Integration\Plugin\Catalog\Model\Category;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\Store;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class SetCategoryCustomUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function setUp(): void
    {
        $this->categoryRepository = \Magento\TestFramework\ObjectManager::getInstance()
            ->create(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadCategories
     */
    public function testCategoryCustomUrl()
    {
        $categoryId = 338;
        $category = $this->categoryRepository->get($categoryId);

        $this->assertEquals('http://localhost/index.php/contact', $category->getUrl());
    }

    public static function loadCategories()
    {
        require __DIR__.'/../../../../_files/categories.php';

        $indexerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Indexer\IndexerRegistry::class);
        $indexerRegistry->get(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID)->reindexAll();
    }

    public static function loadCategoriesRollback()
    {
        require __DIR__.'/../../../../_files/categories_rollback.php';
    }
}
