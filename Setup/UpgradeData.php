<?php

namespace MageSuite\Frontend\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    protected $moduleDataSetupInterface;

    /**
     * @var \Magento\Eav\Setup\EavSetup
     */
    protected $eavSetup;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    public function __construct(
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetupInterface,
        \Magento\Eav\Model\Config $eavConfig

    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->moduleDataSetupInterface = $moduleDataSetupInterface;
        $this->eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetupInterface]);
        $this->eavConfig = $eavConfig;
    }

    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            $this->upgradeToVersion002();
        }

        if (version_compare($context->getVersion(), '0.0.10', '<')) {
            $this->upgradeToVersion010();
        }

        if (version_compare($context->getVersion(), '0.0.12', '<')) {
            $this->upgradeToVersion012();
        }

        if (version_compare($context->getVersion(), '0.0.13', '<')) {
            $this->upgradeToVersion013();
        }

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->upgradeToVersion101();
        }

        $setup->endSetup();
    }

    protected function upgradeToVersion002()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'category_custom_url')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_custom_url',
                [
                    'type' => 'varchar',
                    'label' => 'Category Url',
                    'input' => 'text',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 35,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'show_in_brand_carousel')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'show_in_brand_carousel',
                [
                    'type' => 'int',
                    'label' => 'Show In Brand Carousel',
                    'input' => 'select',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 110,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Display Settings'
                ]
            );
        }
    }

    protected function upgradeToVersion010()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'category_view')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_view',
                [
                    'type' => 'varchar',
                    'input' => 'select',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 130,
                    'source' => 'MageSuite\Frontend\Model\Category\Attribute\Source\CategoryDisplay',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Display Settings'

                ]
            );
        }
    }

    protected function upgradeToVersion012()
    {
        if ($this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'show_in_brand_carousel')) {
            $this->eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY,'show_in_brand_carousel');
        }
    }

    protected function upgradeToVersion013() {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'is_simplified_bundle')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'is_simplified_bundle',
                [
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'type' => 'int',
                    'unique' => false,
                    'group' => 'General',
                    'label' => 'Simplified bundle product',
                    'input' => 'boolean',
                    'default' => false,
                    'required' => false,
                    'sort_order' => 35,
                    'user_defined' => 1,
                    'searchable' => false,
                    'filterable' => false,
                    'filterable_in_search' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'note' => 'Hides bundle configurator on a product page.',
                    'apply_to' => \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE
                ]
            );
        }
    }

    protected function upgradeToVersion101()
    {
        if ($this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'category_view')) {
            $this->eavSetup->updateAttribute(\Magento\Catalog\Model\Category::ENTITY, 'category_view', 'source_model', 'MageSuite\Frontend\Model\Category\Attribute\Source\CategoryDisplay');
        }
    }
}
