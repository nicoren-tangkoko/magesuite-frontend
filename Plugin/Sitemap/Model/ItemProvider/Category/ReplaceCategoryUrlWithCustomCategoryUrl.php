<?php

namespace MageSuite\Frontend\Plugin\Sitemap\Model\ItemProvider\Category;

class ReplaceCategoryUrlWithCustomCategoryUrl
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Sitemap\Model\SitemapItemInterfaceFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Sitemap\Model\SitemapItemInterfaceFactory $itemFactory
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Sitemap\Model\SitemapItemInterfaceFactory $itemFactory,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    )
    {
        $this->resource = $resource;
        $this->itemFactory = $itemFactory;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param \Magento\Sitemap\Model\ItemProvider\Category $category
     * @param $result
     * @param int $storeId
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    public function afterGetItems(\Magento\Sitemap\Model\ItemProvider\Category $category, $result, int $storeId)
    {
        $categoriesIds = array_keys($result);
        $categoriesCustomUrlAttributes = $this->getCategoriesCustomUrlAttributes($categoriesIds, $storeId);

        foreach ($result as $categoryId => $category) {
            if (!isset($categoriesCustomUrlAttributes[$categoryId]['category_custom_url']) || !$categoriesCustomUrlAttributes[$categoryId]['category_custom_url']) {
                continue;
            }
            $result[$categoryId] = $this->itemFactory->create([
                'url' => $categoriesCustomUrlAttributes[$categoryId]['category_custom_url'],
                'updatedAt' => $category->getUpdatedAt(),
                'images' => $category->getImages(),
                'priority' => $category->getPriority(),
                'changeFrequency' => $category->getChangeFrequency()
            ]);
        }

        return $result;
    }

    /**
     * @param array $categoriesIds
     * @param int $storeId
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    public function getCategoriesCustomUrlAttributes(array $categoriesIds, int $storeId): array
    {
        $categoriesIdsString = sprintf('(%s)', implode(',', $categoriesIds));
        $linkField = $this->metadataPool->getMetadata(\Magento\Catalog\Api\Data\CategoryInterface::class)->getLinkField();
        $connection = $this->resource->getConnection();
        $categoryCustomUrlAttributeSelect = $connection->select()->from(
            ['eav_attribute' => 'eav_attribute'],
            []
        )->join(
            ['catalog_category_entity_varchar' => $this->resource->getTableName('catalog_category_entity_varchar')],
            'eav_attribute.attribute_id = catalog_category_entity_varchar.attribute_id'
            . $connection->quoteInto(' AND catalog_category_entity_varchar.store_id = ?', $storeId)
            . sprintf(' AND catalog_category_entity_varchar.%s IN %s', $linkField, $categoriesIdsString),
            [
                'category_custom_url' => 'value',
                'category_id' => $linkField
            ]
        )->where(
            'eav_attribute.entity_type_id = ?',
            \Magento\Catalog\Setup\CategorySetup::CATEGORY_ENTITY_TYPE_ID
        )->where(
            'eav_attribute.attribute_code = ?',
            \MageSuite\Frontend\Helper\Category::CATEGORY_CUSTOM_URL
        );

        $result = $connection
            ->query($categoryCustomUrlAttributeSelect)
            ->fetchAll();

        $categoriesCustomUrlAttributes = $this->convertResult($result);

        return $categoriesCustomUrlAttributes;
    }

    /**
     * @param array|null $result
     * @return array
     */
    private function convertResult(?array $result): array
    {
        if (!$result) {
            return [];
        }

        $categoriesCustomUrlAttributes = [];
        foreach ($result as $categoryAttribute) {
            $categoriesCustomUrlAttributes[$categoryAttribute['category_id']]['category_custom_url'] = $categoryAttribute['category_custom_url'];
        }

        return $categoriesCustomUrlAttributes;
    }
}
