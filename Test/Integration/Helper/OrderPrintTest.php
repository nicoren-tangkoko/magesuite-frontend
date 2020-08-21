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
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    public function setUp(): void
    {
        parent::setUp();

        $this->customer = $this->_objectManager->get(\Magento\Customer\Model\Customer::class);
        $this->customerSession = $this->_objectManager->get(\Magento\Customer\Model\Session::class);
        $this->order = $this->_objectManager->get(\Magento\Sales\Api\Data\OrderInterface::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store sales/general/show_print_button 0
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     */
    public function testHiddingOrderPrintButton()
    {
        $this->customerSession->setCustomerAsLoggedIn($this->getCustomer());

        $this->dispatch(self::ORDER_PRINT_URL . $this->getOrderId());
        $html = $this->getResponse()->getBody();

        $this->assertNotContains('Print Order', $html);
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store sales/general/show_print_button 1
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     */
    public function testShowingOrderPrintButton()
    {
        $this->customerSession->setCustomerAsLoggedIn($this->getCustomer());

        $this->dispatch(self::ORDER_PRINT_URL . $this->getOrderId());
        $html = $this->getResponse()->getBody();

        $this->assertContains('Print Order', $html);
    }

    protected function getOrderId()
    {
        $order = $this->order->loadByIncrementId('100000001');

        return $order->getId();
    }

    protected function getCustomer()
    {
        return $this->customer->load(1);
    }
}
