<?php

namespace MageSuite\Frontend\Test\Integration\Helper;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class ProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \MageSuite\Frontend\Helper\Product
     */
    private $productHelper;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productRepository = $this->objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);

        $this->productHelper = $this->objectManager->get(\MageSuite\Frontend\Helper\Product::class);
    }

    public static function loadProductWithReviewsFixture()
    {
        require __DIR__ . '/../_files/product_with_reviews.php';
    }

    public static function loadProductWithReviewsFixtureRollback()
    {
        require __DIR__ . '/../_files/product_with_reviews_rollback.php';
    }

    public static function loadSaleProductFixture()
    {
        require __DIR__ . '/../_files/sale_product.php';
    }

    public static function loadSaleProductFixtureRollback()
    {
        require __DIR__ . '/../_files/sale_product.php';
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProductWithReviewsFixture
     */
    public function testItReturnsReviewSummary()
    {
        $productId = 555;
        $product = $this->productRepository->getById($productId);

        $reviewSummary = $this->productHelper->getReviewSummary($product, true);

        $this->assertArrayHasKey('data', $reviewSummary);
        $this->assertCount(5, $reviewSummary['data']);
        $this->assertEquals(5, $reviewSummary['data']['maxStars']);
        $this->assertEquals(1, $reviewSummary['data']['count']);

        $this->assertArrayHasKey('votes', $reviewSummary['data']);
        $this->assertCount(5, $reviewSummary['data']['votes']);
        $this->assertEquals(1, $reviewSummary['data']['votes'][2]);

        $this->assertArrayHasKey('ratings', $reviewSummary['data']);

        $this->assertEquals(1, $reviewSummary['data']['ratings'][1]['starsAmount']);
        $this->assertEquals(2, $reviewSummary['data']['ratings'][2]['starsAmount']);
        $this->assertEquals(3, $reviewSummary['data']['ratings'][3]['starsAmount']);

        $this->assertEquals('Quality', $reviewSummary['data']['ratings'][1]['label']);
        $this->assertEquals('Value', $reviewSummary['data']['ratings'][2]['label']);
        $this->assertEquals('Price', $reviewSummary['data']['ratings'][3]['label']);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProductWithReviewsFixture
     */
    public function testItReturnsEmptyReviewSummary()
    {
        $productId = 556;
        $product = $this->productRepository->getById($productId);

        $reviewSummary = $this->productHelper->getReviewSummary($product);

        $this->assertArrayHasKey('data', $reviewSummary);
        $this->assertCount(5, $reviewSummary['data']);

        $this->assertEquals(0, $reviewSummary['data']['count']);

        $this->assertArrayHasKey('votes', $reviewSummary['data']);
        $this->assertCount(5, $reviewSummary['data']['votes']);
        $this->assertEquals(0, $reviewSummary['data']['votes'][2]);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProductWithReviewsFixture
     */
    public function testItReturnsCorrectAddToCartUrl()
    {
        $product = $this->productRepository->get('first_product');

        $url = $this->productHelper->getAddToCartUrl($product->getId());

        $this->assertEquals('http://localhost/index.php/checkout/cart/add/product/555/', $url);
    }

    public static function loadConfigurableProduct()
    {
        require __DIR__ . '/../_files/configurable_product.php';
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/ConfigurableProduct/_files/configurable_products.php
     * @magentoDataFixture loadConfigurableProduct
     */
    public function testItReturnsCorrectConfigurableDiscounts()
    {
        $product = $this->productRepository->get('configurable');

        $configurableDiscounts = $this->productHelper->getConfigurableDiscounts($product);

        $this->assertEquals([10 => 90, 20 => 68], $configurableDiscounts);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/ConfigurableProduct/_files/configurable_products.php
     * @magentoDataFixture loadConfigurableProduct
     */
    public function testItReturnsCorrectSalePercentageForConfigurableProduct()
    {
        $product = $this->productRepository->get('configurable');

        $salePercentage = $this->productHelper->getSalePercentage($product);

        $this->assertEquals(90, $salePercentage);
    }

    public static function loadBundleProduct()
    {
        require __DIR__ . '/../_files/bundle_product.php';
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Bundle/_files/product.php
     * @magentoDataFixture loadBundleProduct
     */
    public function testItReturnsCorrectSalePercentageForBundleProduct()
    {
        $product = $this->productRepository->get('bundle-product');

        $salePercentage = $this->productHelper->getSalePercentage($product);

        $this->assertEquals(35, $salePercentage);
    }

    public static function loadProductWithTax()
    {
        require __DIR__ . '/../_files/product_with_tax.php';
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadProductWithTax
     * @magentoConfigFixture current_store tax/calculation/price_includes_tax 0
     * @magentoConfigFixture current_store tax/display/type 2
     */
    public function testItReturnsCorrectSalePercentageForProductWithTax()
    {
        $product = $this->productRepository->get('product_with_tax');
        $salePercentage = $this->productHelper->getSalePercentage($product);
        $this->assertEquals(50, $salePercentage);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadSaleProductFixture
     * @dataProvider getOnSaleDates
     * @param $specialPrice
     * @param $specialPriceFrom
     * @param $specialPriceTo
     * @param $getPrice
     * @param $getFinalPrice
     * @param $expected
     */
    public function testItReturnsIsOnSale($specialPrice, $specialPriceFrom, $specialPriceTo, $getPrice, $getFinalPrice, $expected)
    {
        $productStub = $this->prepareProductStubForOnSale($specialPrice, $specialPriceFrom, $specialPriceTo, $getPrice, $getFinalPrice);

        $this->assertEquals($expected, $this->productHelper->isOnSale($productStub));
    }

    protected function prepareProductStubForOnSale($specialPrice, $specialPriceFrom, $specialPriceTo, $getPrice, $getFinalPrice)
    {
        $product = $this->productRepository->get('sale_product');

        $product->setSpecialPrice($specialPrice);
        $product->setSpecialFromDate($specialPriceFrom);
        $product->setSpecialToDate($specialPriceTo);
        $product->setPrice($getPrice);
        $product->save();

        $product->reindex();
        $product->priceReindexCallback();

        return $product;
    }

    public static function getOnSaleDates()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        /** @var \Magento\Framework\App\ProductMetadataInterface $magentoProductMetadata */
        $magentoProductMetadata = $objectManager->get(\Magento\Framework\App\ProductMetadataInterface::class);

        if($magentoProductMetadata->getEdition() == \MageSuite\Frontend\Helper\Product::MAGENTO_ENTERPRISE) {
            return [
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 200, 100, true],
                [100, date('Y-m-d 00:00:00', strtotime('+7 days')), date('Y-m-d 00:00:00', strtotime('+17 days')), 200, 100, false],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                [300, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                ['', date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                [100, null, date('Y-m-d 00:00:00', strtotime('+3 days')), 200, 100, true],
                [100, null, date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                [100, date('Y-m-d 00:00:00', strtotime('-3 days')), null, 200, 100, true],
                ['', null, null, 200, 100, false],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 0, 100, false]
            ];
        } else {
            return [
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 200, 100, true],
                [100, date('Y-m-d 00:00:00', strtotime('+7 days')), date('Y-m-d 00:00:00', strtotime('+17 days')), 200, 100, false],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                [300, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                ['', date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                [100, null, date('Y-m-d 00:00:00', strtotime('+3 days')), 200, 100, true],
                [100, null, date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, false],
                [100, date('Y-m-d 00:00:00', strtotime('-3 days')), null, 200, 100, true],
                ['', null, null, 200, 100, false],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 0, 100, false]
            ];
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture loadSaleProductFixture
     * @dataProvider getPercentage
     * @param $specialPrice
     * @param $specialPriceFrom
     * @param $specialPriceTo
     * @param $getPrice
     * @param $getFinalPrice
     * @param $customFinalPrice
     * @param $expected
     */
    public function testItReturnsCorrectPercentage($specialPrice, $specialPriceFrom, $specialPriceTo, $getPrice, $getFinalPrice, $customFinalPrice, $expected)
    {
        $productStub = $this->prepareProductStubForOnSale($specialPrice, $specialPriceFrom, $specialPriceTo, $getPrice, $getFinalPrice);

        $this->assertEquals($expected, $this->productHelper->getSalePercentage($productStub, $customFinalPrice));
    }

    public static function getPercentage()
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        /** @var \Magento\Framework\App\ProductMetadataInterface $magentoProductMetadata */
        $magentoProductMetadata = $objectManager->get(\Magento\Framework\App\ProductMetadataInterface::class);

        if($magentoProductMetadata->getEdition() == \MageSuite\Frontend\Helper\Product::MAGENTO_ENTERPRISE) {

            return [
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 200, 100, null, 50],
                [100, date('Y-m-d 00:00:00', strtotime('+7 days')), date('Y-m-d 00:00:00', strtotime('+17 days')), 200, 100, null, false],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                [300, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                ['', date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                [150, null, date('Y-m-d 00:00:00', strtotime('+3 days')), 500, 150, null, 70],
                [100, null, date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                [10, date('Y-m-d 00:00:00', strtotime('-3 days')), null, 300, 10, null, 97],
                ['', null, null, 200, 100, null, false],
                ['', null, null, 200, 100, 50, 75],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 200, 100, 50, 75],
            ];
        } else {
            return [
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 200, 100, null, 50],
                [100, date('Y-m-d 00:00:00', strtotime('+7 days')), date('Y-m-d 00:00:00', strtotime('+17 days')), 200, 100, null, false],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                [300, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                ['', date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                [150, null, date('Y-m-d 00:00:00', strtotime('+3 days')), 500, 150, null, 70],
                [100, null, date('Y-m-d 00:00:00', strtotime('-3 days')), 200, 100, null, false],
                [10, date('Y-m-d 00:00:00', strtotime('-3 days')), null, 300, 10, null, 97],
                ['', null, null, 200, 100, null, false],
                ['', null, null, 200, 100, 50, 75],
                [100, date('Y-m-d 00:00:00', strtotime('-7 days')), date('Y-m-d 00:00:00', strtotime('+7 days')), 200, 100, 50, 75],
            ];
        }
    }



}