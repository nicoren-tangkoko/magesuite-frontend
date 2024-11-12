<?php
declare(strict_types=1);

namespace MageSuite\Frontend\Plugin;

class SortAttributeOptions
{
    protected ?array $items = null;
    protected ?array $sortedItems = null;
    protected array $options = [];

    protected \Magento\Framework\App\ResourceConnection $resource;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function aroundGetAttributeOptions(\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $subject, $proceed, $superAttribute, $productId)
    {
        $attributeId = $superAttribute->getAttributeId();
        if (!isset($this->items[$productId][$attributeId])) {
            $this->items[$productId][$attributeId] = $proceed($superAttribute, $productId);
        }

        if (empty($this->items[$productId][$attributeId])) {
            return $this->items[$productId][$attributeId];
        }

        if (isset($this->sortedItems[$productId][$attributeId])) {
            return $this->sortedItems[$productId][$attributeId];
        }

        $items = $this->items[$productId][$attributeId];
        $sortOrder = array_flip(array_keys($this->getOptions((int)$superAttribute->getAttributeId())));

        if (empty($sortOrder)) {
            $this->sortedItems[$productId][$attributeId] = $items;
            return $items;
        }

        uasort($items, function ($firstElement, $secondElement) use ($sortOrder) {
            $firstIndex = $firstElement['value_index'] ?? 0;
            $secondIndex = $secondElement['value_index'] ?? 0;

            $firstValue = isset($sortOrder[$firstIndex]) ? (int)$sortOrder[$firstIndex] : 0;
            $secondValue = isset($sortOrder[$secondIndex]) ? (int)$sortOrder[$secondIndex] : 0;

            return $firstValue <=> $secondValue;
        });

        $this->sortedItems[$productId][$attributeId] = $items;
        return $items;
    }

    protected function getOptions(int $attributeId): array
    {
        if (array_key_exists($attributeId, $this->options)) {
            return $this->options[$attributeId];
        }

        $connection = $this->resource->getConnection();
        $select = $connection->select()->from(
            ['attribute_opt' => $this->resource->getTableName('eav_attribute_option')],
            ['option_id', 'sort_order']
        )->where(
            'attribute_opt.attribute_id = ?',
            $attributeId
        )->order(
            'attribute_opt.sort_order ASC'
        );
        $this->options[$attributeId] = $connection->fetchPairs($select);

        return $this->options[$attributeId];
    }
}
