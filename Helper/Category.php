<?php

namespace MageSuite\Frontend\Helper;

use MageSuite\ContentConstructorFrontend\DataProviders\ProductCarouselDataProvider;

class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CACHE_LIFETIME = 86400;
    const CACHE_TAG = 'layered_navigation_tree_%s_%s_%s';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \MageSuite\Frontend\Model\Category\Tree
     */
    protected $categoryTree;

    /**
     * @var ProductCarouselDataProvider
     */
    protected $productDataProvider;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonDecoder;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $categoryResource;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \MageSuite\Frontend\Model\Category\Tree $categoryTree,
        ProductCarouselDataProvider $productDataProvider,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category $categoryResource,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->registry = $registry;
        $this->categoryTree = $categoryTree;
        $this->productDataProvider = $productDataProvider;
        $this->jsonDecoder = $jsonDecoder;
        $this->cache = $cache;
        $this->storeManager = $storeManager;
        $this->categoryResource = $categoryResource;
        $this->categoryRepository = $categoryRepository;
        $this->eavConfig = $eavConfig;
    }

    public function getCategoryNode($category = null, $returnCurrent = false)
    {
        if (!$category) {
            $category = $this->registry->registry('current_category');

            if (!$category) {
                return false;
            }
        }

        $cacheTag = sprintf(self::CACHE_TAG, $category->getId(), (int)$returnCurrent, $this->storeManager->getStore()->getId());

        $categoryNode = unserialize($this->cache->load($cacheTag));

        if (!$categoryNode) {

            $configuration = [
                'root_category_id' => 2,
                'only_included_in_menu' => 0
            ];

            $categoryTreeId = ($returnCurrent or $category->getLevel() == 2) ? $category->getId() : $category->getParentId();
            $categoryNode = $this->categoryTree->getCategoryTree($configuration, $categoryTreeId);

            $this->cache->save(serialize($categoryNode), $cacheTag, ['layered_navigation_tree'], self::CACHE_LIFETIME);
        }

        $categoryNode['current'] = true;
        if ($category->getLevel() > 2) {
            $categoryNode['children'][$category->getId()]['current'] = true;
        }

        return $categoryNode;
    }


    protected function getFeaturedProductsIds($category)
    {
        $featuredProducts = $category->getFeaturedProducts();

        if ($featuredProducts == '{}') {
            $featuredProducts = $this->categoryResource
                ->getAttributeRawValue($category->getId(), 'featured_products', 0);
        }

        if (!$featuredProducts or $featuredProducts == '{}') {
            return [];
        }

        return array_keys($this->jsonDecoder->decode($featuredProducts));
    }

    public function getFeaturedProducts($category)
    {
        $featuredProductsIds = $this->getFeaturedProductsIds($category);

        if (empty($featuredProductsIds)) {
            return [];
        }

        $criteria = ['product_ids' => $featuredProductsIds];
        $products = $this->productDataProvider->getProducts($criteria);

        return $products;
    }

    public function prepareCategoryCustomUrl($customUrl)
    {
        if (!$customUrl) {
            return null;
        }

        if (strpos($customUrl, 'http') !== false) {
            return $customUrl;
        }

        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        return $baseUrl . ltrim($customUrl, '/');
    }

    public function getImageTeaser($category)
    {
        $url = false;
        $image = is_object($category) ? $category->getImageTeaser() : $category;

        if ($image) {
            if (is_string($image)) {
                $url = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $image;
            } elseif (is_array($image) && isset($image[0]) && isset($image[0]['name'])) {
                $url = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'catalog/category/' . $image[0]['name'];
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }

        return $url;
    }

    public function getCategoryFilterIcon($filterItem)
    {
        if (!$filterItem instanceof \Smile\ElasticsuiteCatalog\Model\Layer\Filter\Item\Category) {
            return null;
        }

        try {
            $categoryId = (int)$filterItem->getValueString();
            $category = $this->categoryRepository->get($categoryId);

            return $this->getCategoryIcon($category);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }

    public function getCategoryView()
    {
        $category = $this->registry->registry('current_category');

        if (!$category) {
            return false;
        }
        $view = $category->getCustomAttribute('category_view');

        if (!$view) {
            return false;
        }

        return $view->getValue();
    }
}

