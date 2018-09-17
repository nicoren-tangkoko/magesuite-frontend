<?php

namespace MageSuite\Frontend\Helper;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        parent::__construct($context);

        $this->session = $session;
        $this->remoteAddress = $remoteAddress;
    }

    public function getCustomerIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    public function isLoggedIn()
    {
        return $this->session->isLoggedIn();
    }

}
