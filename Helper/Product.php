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

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \MageSuite\Frontend\Helper\Review $reviewHelper,
        \Magento\Framework\App\ProductMetadataInterface $magentoProductMetadata
    ) {
        parent::__construct($context);
        $this->reviewHelper = $reviewHelper;
        $this->magentoProductMetadata = $magentoProductMetadata;
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

    public function getAddToCartUrl($productId)
    {
        $routeParams = [
            'product' => $productId,
            '_secure' => $this->_getRequest()->isSecure()
        ];

        return $this->_getUrl('checkout/cart/add', $routeParams);
    }

    public function isMagentoEnterprise()
    {
        return $this->magentoProductMetadata->getEdition() == self::MAGENTO_ENTERPRISE;
    }
}
