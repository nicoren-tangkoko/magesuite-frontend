<?php

namespace MageSuite\Frontend\Model\Category;

class Tree
{
    const CACHE_LIFETIME = 86400;
    const CACHE_TAG = 'category_tree_%s_%s_%s';

    private $rootCategoryId;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \MageSuite\ContentConstructorFrontend\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \MageSuite\ContentConstructorFrontend\Helper\Category $categoryHelper,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryHelper =  $categoryHelper;
        $this->cache = $cache;
        $this->storeManager = $storeManager;
    }

    private function buildTree($collection, $currentCategories)
    {
        $flat = [];
        $categories = [];

        foreach ($collection as $category) {
            $additionalData = [
                'url' => $category->getUrl(),
                'products_count' => $this->categoryHelper->getNumberOfProducts($category),
                'current' => false,
                'parents' => [],
                'children' => []
            ];

            $categoryData = array_merge($category->getData(), $additionalData);

            $flat[$categoryData['entity_id']] = $categoryData;
            $categories[$categoryData['parent_id']][$categoryData['entity_id']] = $categoryData;
        }

        $categoryFlat = [];

        $builder = function ($siblings) use (&$builder, $categories, &$categoryFlat, $flat, $currentCategories) {
            foreach ($siblings as $k => $sibling) {
                if (!$sibling['is_active']) {
                    continue;
                }

                $id = $sibling['entity_id'];

                if (isset($categories[$id])) {
                    $sibling['children'] = $builder($categories[$id]);
                }

                $path = $this->preparePath($sibling['path']);

                foreach ($path as $categoryId) {
                    if ($id == $categoryId) {
                        continue;
                    }
                    $sibling['parents'][$categoryId] = $flat[$categoryId];
                }

                $siblings[$k] = $sibling;
                $categoryFlat[$id] = &$siblings[$k];
            }

            return $siblings;
        };

        $tree = $builder($categories[$this->rootCategoryId]);

        return [
            'tree' => $tree,
            'flat' => $categoryFlat
        ];
    }

    public function getCategoryTree($configuration = [], $categoryId = null, $currentCategories = [], $categoryTag = null)
    {
        $this->rootCategoryId = isset($configuration['root_category_id']) ? $configuration['root_category_id'] : 0;
        $onlyIncludedInMenu = isset($configuration['only_included_in_menu']) ? $configuration['only_included_in_menu'] : 0;

        if (!$this->rootCategoryId) {
            $this->rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        }

        if ($this->rootCategoryId == $categoryId) {
            return false;
        }

        $cacheTag = sprintf(
            self::CACHE_TAG,
            $onlyIncludedInMenu,
            $this->rootCategoryId,
            $this->storeManager->getStore()->getId()
        );

        $categoryTree = unserialize($this->cache->load($cacheTag));

        if (!$categoryTree or ($categoryId and !isset($categoryTree['flat'][$categoryId]))) {
            $categoryCollection = $this->getCategoriesFromCollection($configuration);
            $categoryTree = $this->buildTree($categoryCollection, $currentCategories);

            $this->cache->save(serialize($categoryTree), $cacheTag, ['categories_tree'], self::CACHE_LIFETIME);
        }

        if ($categoryId) {
            $category = isset($categoryTree['flat'][$categoryId]) ? $categoryTree['flat'][$categoryId] : false;

            if (is_array($category)) {
                $category = $this->markCurrentCategories($category, $currentCategories);
            }

            return $category;
        }

        return $categoryTree['tree'];
    }

    protected function getCategoriesFromCollection($configuration)
    {
        $categoryCollection = $this->categoryCollectionFactory->create();

        $categoryCollection->addFieldToFilter('is_active', 1);
        $categoryCollection->setOrder('position');

        if (isset($configuration['only_included_in_menu']) && $configuration['only_included_in_menu']) {
            $categoryCollection->addFieldToFilter('include_in_menu', 1);
        }

        $categoryCollection->addAttributeToSelect('*');

        return $categoryCollection->getItems();
    }

    protected function preparePath($path)
    {
        $pathIds = explode('/', $path);

        $rootCategoryPosition = array_search($this->rootCategoryId, $pathIds);
        $pathIds = array_slice($pathIds, $rootCategoryPosition+1, -1);

        return $pathIds;
    }

    protected function markCurrentCategories($category, $currentCategories)
    {
        if (in_array($category['entity_id'], $currentCategories)) {
            $category['current'] = true;
        }

        if (!empty($category['parents'])) {
            foreach ($category['parents'] as &$parent) {
                $parent = $this->markCurrentCategories($parent, $currentCategories);
            }
        }

        if (!empty($category['children'])) {
            foreach ($category['children'] as &$child) {
                $child = $this->markCurrentCategories($child, $currentCategories);
            }
        }

        return $category;
    }
}
