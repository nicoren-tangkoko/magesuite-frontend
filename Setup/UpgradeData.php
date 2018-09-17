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

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $this->upgradeToVersion003();
        }

        if (version_compare($context->getVersion(), '0.0.4', '<')) {
            $this->upgradeToVersion004();
        }

        if (version_compare($context->getVersion(), '0.0.7', '<')) {
            $this->upgradeToVersion007();
        }

        if (version_compare($context->getVersion(), '0.0.8', '<')) {
            $this->upgradeToVersion008();
        }

        if (version_compare($context->getVersion(), '0.0.9', '<')) {
            $this->upgradeToVersion009();
        }

        if (version_compare($context->getVersion(), '0.0.10', '<')) {
            $this->upgradeToVersion010();
        }
        
        if (version_compare($context->getVersion(), '0.0.11', '<')) {
            $this->upgradeToVersion011();
        }

        if (version_compare($context->getVersion(), '0.0.12', '<')) {
            $this->upgradeToVersion012();
        }

        if (version_compare($context->getVersion(), '0.0.13', '<')) {
            $this->upgradeToVersion013();
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

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'do_not_expand_flyout')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'do_not_expand_flyout',
                [
                    'type' => 'int',
                    'label' => 'Do not expand flyout',
                    'input' => 'select',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 120,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Display Settings'
                ]
            );
        }
    }

    protected function upgradeToVersion003()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'category_icon')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_icon',
                [
                    'type' => 'varchar',
                    'label' => 'Category Icon',
                    'backend' => 'MageSuite\Frontend\Model\Category\Attribute\Backend\Icon',
                    'input' => 'image',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 35,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Content'
                ]
            );
        }
    }

    protected function upgradeToVersion004()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'featured_products_header')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'featured_products_header',
                [
                    'type' => 'varchar',
                    'label' => 'Header',
                    'input' => 'text',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 10,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Featured Products'
                ]
            );
        }

        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'featured_products')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'featured_products',
                [
                    'type' => 'text',
                    'label' => 'Category Featured Products',
                    'input' => 'text',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 20,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'Featured Products'
                ]
            );
        }
    }

    protected function upgradeToVersion007()
    {
        if ($this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'image_teaser')) {
            $this->eavSetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'image_teaser');
            $this->eavConfig->clear();
        }

        $this->eavSetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'image_teaser',
            [
                'type' => 'varchar',
                'label' => 'Image',
                'backend' => 'MageSuite\Frontend\Model\Category\Attribute\Backend\ImageTeaser',
                'input' => 'image',
                'visible' => true,
                'required' => false,
                'sort_order' => 10,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Image Teaser'
            ]
        );

        $attributes = [
            'image_teaser_headline' => ['label' => 'Headline', 'type' => 'varchar', 'input' => 'text', 'sort_order' => 20],
            'image_teaser_subheadline' => ['label' => 'Subheadline', 'type' => 'varchar', 'input' => 'text', 'sort_order' => 30],
            'image_teaser_paragraph' => ['label' => 'Paragraph', 'type' => 'text', 'input' => 'textarea', 'sort_order' => 40],
            'image_teaser_button_label' => ['label' => 'Button Label', 'type' => 'varchar', 'input' => 'text', 'sort_order' => 50],
            'image_teaser_button_link' => ['label' => 'Button Link', 'type' => 'varchar', 'input' => 'text', 'sort_order' => 60]
        ];

        foreach($attributes AS $attributeCode => $attribute){
            if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, $attributeCode)) {
                $this->eavSetup->addAttribute(
                    \Magento\Catalog\Model\Category::ENTITY,
                    $attributeCode,
                    [
                        'type' => $attribute['type'],
                        'label' => $attribute['label'],
                        'input' => $attribute['input'],
                        'visible' => true,
                        'required' => false,
                        'sort_order' => $attribute['sort_order'],
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'Image Teaser'
                    ]
                );
            }
        }
    }

    protected function upgradeToVersion008()
    {
        $this->eavSetup->updateAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'category_icon',
            'backend_model',
            'Magento\Catalog\Model\Category\Attribute\Backend\Image'
        );

        $this->eavSetup->updateAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'image_teaser',
            'backend_model',
            'Magento\Catalog\Model\Category\Attribute\Backend\Image'
        );
    }

    protected function upgradeToVersion009()
    {
        if (!$this->eavSetup->getAttributeId(\Magento\Catalog\Model\Category::ENTITY, 'category_identifier')) {
            $this->eavSetup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_identifier',
                [
                    'type' => 'varchar',
                    'label' => 'Category Identifier',
                    'input' => 'text',
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 31,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'group' => 'General Information'
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

    /**
     * This is a fix for missing 0.0.8 and 0.0.9 migrations in 2.1 branch.
     * Because on 2.1 we bumped version up to 0.0.10, there's need to re-run both missing migrations.
     */
    protected function upgradeToVersion011()
    {
        $this->upgradeToVersion008();
        $this->upgradeToVersion009();
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
}
