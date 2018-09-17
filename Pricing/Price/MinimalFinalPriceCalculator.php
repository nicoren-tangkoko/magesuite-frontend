<?php

namespace MageSuite\Frontend\Pricing\Price;

use Magento\Bundle\Pricing\Price\FinalPrice;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Amount\AmountInterface;
use Magento\Framework\Pricing\SaleableInterface;

class MinimalFinalPriceCalculator implements MinimalPriceCalculatorInterface
{

    /**
     * Price Calculator interface.
     *
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @param CalculatorInterface $calculator
     */
    public function __construct(CalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * Get raw value for "as low as" price.
     *
     * @param SaleableInterface $saleableItem
     * @return float|null
     */
    public function getValue(SaleableInterface $saleableItem)
    {
        /** @var \Magento\Catalog\Pricing\Price\FinalPrice $price */
        $price = $saleableItem->getPriceInfo()->getPrice(FinalPrice::PRICE_CODE);
        return $price->getMinimalPrice()->getValue();
    }

    /**
     * Return structured object with "as low as" value.
     *
     * @param SaleableInterface $saleableItem
     * @return AmountInterface|null
     */
    public function getAmount(SaleableInterface $saleableItem)
    {
        $value = $this->getValue($saleableItem);

        return $value === null ? null : $this->calculator->getAmount($value, $saleableItem);
    }
}
