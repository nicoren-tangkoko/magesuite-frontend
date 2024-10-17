<?php

namespace MageSuite\Frontend\Plugin\Swatches;

class MissingZeroValueSwatch
{
    /**
     * Default store ID
     */
    const DEFAULT_STORE_ID = 0;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory
     */
    protected $swatchCollectionFactory;

    protected $swatchesCache = [];

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory $swatchCollectionFactory
    ) {
        $this->storeManager = $storeManager;
        $this->swatchCollectionFactory = $swatchCollectionFactory;
    }

    public function aroundGetSwatchesByOptionsId(\Magento\Swatches\Helper\Data $subject, callable $proceed, array $optionIds)
    {
        /**
         * Core method contains bug which do not allow to add swatches with zero value.
         * Modification on line 52:
         * Change:
         * $item['value'] != null
         */
        $swatches = $this->getCachedSwatches($optionIds);

        if (count($swatches) !== count($optionIds)) {
            $swatchOptionIds = array_diff($optionIds, array_keys($swatches));
            /** @var \Magento\Swatches\Model\ResourceModel\Swatch\Collection $swatchCollection */
            $swatchCollection = $this->swatchCollectionFactory->create();
            $swatchCollection->addFilterByOptionsIds($swatchOptionIds);

            $swatches = [];
            $currentStoreId = $this->storeManager->getStore()->getId();
            foreach ($swatchCollection as $item) {
                if ($item['type'] != \Magento\Swatches\Model\Swatch::SWATCH_TYPE_TEXTUAL) {
                    $swatches[$item['option_id']] = $item->getData();
                } elseif ($item['store_id'] == $currentStoreId && $item['value'] != null) {
                    $fallbackValues[$item['option_id']][$currentStoreId] = $item->getData();
                } elseif ($item['store_id'] == self::DEFAULT_STORE_ID) {
                    $fallbackValues[$item['option_id']][self::DEFAULT_STORE_ID] = $item->getData();
                }
            }

            if (!empty($fallbackValues)) {
                $swatches = $this->addFallbackOptions($fallbackValues, $swatches);
            }

            $this->setCachedSwatches($swatchOptionIds, $swatches);
        }

        return array_filter($this->getCachedSwatches($optionIds));
    }

    /**
     * @param array $fallbackValues
     * @param array $swatches
     * @return array
     */
    private function addFallbackOptions(array $fallbackValues, array $swatches)
    {
        $currentStoreId = $this->storeManager->getStore()->getId();
        foreach ($fallbackValues as $optionId => $optionsArray) {
            if (isset($optionsArray[$currentStoreId])) {
                $swatches[$optionId] = $optionsArray[$currentStoreId];
            } else {
                $swatches[$optionId] = $optionsArray[self::DEFAULT_STORE_ID];
            }
        }

        return $swatches;
    }

    protected function getCachedSwatches(array $optionIds)
    {
        return array_intersect_key($this->swatchesCache, array_combine($optionIds, $optionIds));
    }

    protected function setCachedSwatches(array $optionIds, array $swatches)
    {
        foreach ($optionIds as $optionId) {
            $this->swatchesCache[$optionId] = $swatches[$optionId] ?? null;
        }
    }
}
