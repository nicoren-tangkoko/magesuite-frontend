<?php

namespace MageSuite\Frontend\Test\Integration\Helper;

/**
 * @magentoDbIsolation enabled
 * @magentoAppIsolation enabled
 */
class OrderPrintTest extends \Magento\TestFramework\TestCase\AbstractController
{
    const ORDER_PRINT_URL = 'sales/order/view/order_id/';

    /**
     * @var \Magento\Customer\Model\Customer $customer
     */
    private $customer;

    /**
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private $customerSession;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface $order
     */
    private $order;

    public function setUp()
    {
        parent::setUp();

        $this->customer = $this->_objectManager->get(\Magento\Customer\Model\Customer::class);
        $this->customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
        $this->order = $this->_objectManager->get(\Magento\Sales\Api\Data\OrderInterface::class);
    }

    /**
     * @magentoConfigFixture current_store order/order_print/enable 0
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     */
    public function testHiddingOrderPrintButton()
    {
        $customer = $this->customer->load(1);
        $this->customerSession->setCustomerAsLoggedIn($customer);
        $order = $this->order->loadByIncrementId('100000001');

        $this->dispatch(self::ORDER_PRINT_URL . $order->getId());
        $html = $this->getResponse()->getBody();

        $this->assertNotContains('Print Order', $html);
    }

    /**
     * @magentoConfigFixture current_store order/order_print/enable 1
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     */
    public function testShowingOrderPrintButton()
    {
        $customer = $this->customer->load(1);
        $this->customerSession->setCustomerAsLoggedIn($customer);
        $order = $this->order->loadByIncrementId('100000001');

        $this->dispatch(self::ORDER_PRINT_URL . $order->getId());
        $html = $this->getResponse()->getBody();

        $this->assertContains('Print Order', $html);
    }
}
