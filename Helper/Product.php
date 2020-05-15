<?php

namespace MageSuite\Frontend\Helper;

class Product extends \Magento\Framework\App\Helper\AbstractHelper
{
    const SPECIAL_PRICE = 'special';
    const REGULAR_PRICE = 'regular';
    const MAGENTO_ENTERPRISE = 'Enterprise';

    /**
     * @var \MageSuite\Frontend\Helper\Review
     */
    protected $reviewHelper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $magentoProductMetadata;

    /**
     * @var \MageSuite\Frontend\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \MageSuite\Frontend\Helper\Review $reviewHelper,
        \Magento\Framework\App\ProductMetadataInterface $magentoProductMetadata,
        \MageSuite\Frontend\Helper\Configuration $configuration
    ) {
        parent::__construct($context);
        $this->reviewHelper = $reviewHelper;
        $this->magentoProductMetadata = $magentoProductMetadata;
        $this->configuration = $configuration;
    }

    public function getReviewSummary($product, $includeVotes = false)
    {
        return $this->reviewHelper->getReviewSummary($product, $includeVotes);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string|null $date
     * @return bool
     */
    public function isNew($product, $date = null)
    {
        $newsFromDate = $product->getNewsFromDate();
        $newsToDate = $product->getNewsToDate();
        $date = $date ?: date('Y-m-d');

        $fromTimestamp = strtotime($newsFromDate);
        $toTimestamp = strtotime($newsToDate);
        $dateTimestamp = strtotime($date);

        if (!$fromTimestamp && !$toTimestamp) {
            return false;
        }

        if (!$fromTimestamp && $dateTimestamp <= $toTimestamp) {
            return true;
        }

        if (!$toTimestamp && $dateTimestamp >= $fromTimestamp) {
            return true;
        }

        if ($dateTimestamp >= $fromTimestamp && $dateTimestamp <= $toTimestamp) {
            return true;
        }

        return false;
    }

    public function isOnSale($product, $finalPrice = null)
    {
        if (in_array($product->getTypeId(), [\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE, \Magento\Bundle\Model\Product\Type::TYPE_CODE])) {
            return $this->checkIsProductOnSale($product, $finalPrice);
        }

        if ($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $simpleProducts = $product->getTypeInstance()->getUsedProducts($product);

            foreach ($simpleProducts as $simpleProduct) {
                $isProductOnSale = $this->checkIsProductOnSale($simpleProduct, $finalPrice);

                if (!$isProductOnSale) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    public function checkIsProductOnSale($product, $finalPrice = null)
    {
        
        if (empty($finalPrice)) {
            $finalPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)->getAmount()->getValue();
        }

        $productPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getAmount()->getValue();

        if (empty($productPrice)) {
            return false;
        }

        if ($productPrice > $finalPrice) {
            return true;
        }

        return false;
    }

    public function getSalePercentage($product, $finalPrice = null)
    {
        if (empty($finalPrice)) {
            $finalPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE)->getAmount()->getValue();
        }

        if (!$this->isOnSale($product, $finalPrice)) {
            return false;
        }

        $regularPrice = $product->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getAmount()->getValue();

        if (!$regularPrice && !in_array($product->getTypeId(), [\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE, \Magento\Bundle\Model\Product\Type::TYPE_CODE])) {
            return false;
        }

        switch($product->getTypeId()) {
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                $configurableDiscounts = $this->getConfigurableDiscounts($product);
                $roundDiscount = max($configurableDiscounts);
                break;
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                $roundDiscount = round(100 - $product->getSpecialPrice());
                break;
            default:
                $roundDiscount = $this->calculateDiscountPercent($regularPrice, $finalPrice);
        }

        if ((int)$roundDiscount >= $this->configuration->getMinimalSalePercentage()) {
            return $roundDiscount;
        }

        return false;
    }

    public function getConfigurableDiscounts($product)
    {
        $configurableDiscounts = [];
        
        if (! $product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            return [];
        } 
        
        if($product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $simpleProducts = $product->getTypeInstance()->getUsedProducts($product);
    
            foreach ($simpleProducts as $simpleProduct) {
                $configurableDiscounts[$simpleProduct->getId()] = $this->calculateDiscountPercent($simpleProduct->getPrice(), $simpleProduct->getFinalPrice());
            }
    
            if(empty($configurableDiscounts)){
                $configurableDiscounts[$product->getId()] = $this->calculateDiscountPercent($product->getPrice(), $product->getFinalPrice());
            }
        }

        return $configurableDiscounts;
    }

    public function getDisplayDiscountBadgesPerProduct(){
        return $this->configuration->getDisplayDiscountBadgesPerProduct();
    }

    public function getAddToCartUrl($productId)
    {
        $routeParams = [
            'product' => $productId,
            '_secure' => $this->_getRequest()->isSecure()
        ];

        return $this->_getUrl('checkout/cart/add', $routeParams);
    }

    public function getSaleOrigin($product)
    {
        if ($product->getSpecialPrice() && $this->isOnSale($product)) {
            return self::SPECIAL_PRICE;
        }

        return self::REGULAR_PRICE;
    }

    public function isMagentoEnterprise()
    {
        return $this->magentoProductMetadata->getEdition() == self::MAGENTO_ENTERPRISE;
    }

    private function calculateDiscountPercent($regularPrice, $finalPrice){
        return round((($regularPrice - $finalPrice) / $regularPrice) * 100, 0);
    }
}
