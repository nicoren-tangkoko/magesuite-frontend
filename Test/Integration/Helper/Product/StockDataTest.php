<?php

namespace MageSuite\Frontend\Test\Integration\Helper\Product;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class StockDataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @var \MageSuite\Frontend\Helper\Product\StockData
     */
    private $stockDataHelper;

    /**
     * @var \MageSuite\ContentConstructorFrontend\DataProviders\ProductCarouselDataProvider
     */
    private $dataProvider;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->stockDataHelper = $this->objectManager->get(\MageSuite\Frontend\Helper\Product\StockData::class);

        $this->dataProvider = $this->objectManager->get(\MageSuite\ContentConstructorFrontend\DataProviders\ProductCarouselDataProvider::class);
    }

    public static function loadProductsFixture()
    {
        require __DIR__ . '/../../_files/products.php';
    }

    public static function loadProductsFixtureRollback()
    {
        require __DIR__ . '/../../_files/products_rollback.php';
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProductsFixture
     * @magentoConfigFixture current_store cataloginventory/options/show_out_of_stock 1
     */
    public function testItReturnsCorrectProductsData()
    {
        $products = $this->dataProvider->getProducts(['category_id' => 333]);

        $expected = [
            'in_stock_with_qty' => ['stock' => true, 'qty' => 100],
            'in_stock_without_qty' => ['stock' => false, 'qty' => 0],
            'out_of_stock_with_qty' => ['stock' => false, 'qty' => 100]
        ];

        $result = [];
        foreach($products as $product){
            $result[$product['sku']] = ['stock' => $product['stock'], 'qty' => $product['qty']];
        }

        $this->assertEquals($expected, $result);
    }
}
