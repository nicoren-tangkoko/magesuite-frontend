<?php

namespace MageSuite\Frontend\Model\ResourceModel\Category;

class Collection
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool
    )
    {
        $this->resource = $resource;
        $this->metadataPool = $metadataPool;
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
        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
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
                'category_custom_url' => 'value'
            ]
        )->join(
            ['catalog_category_entity' => $this->resource->getTableName('catalog_category_entity')],
            sprintf('catalog_category_entity_varchar.%1$s = catalog_category_entity.%1$s', $linkField),
            [
                'category_id' => 'entity_id'
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
