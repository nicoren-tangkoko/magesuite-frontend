<?php

namespace MageSuite\Frontend\Helper;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\App\Http\Context $httpContext
    ) {
        parent::__construct($context);

        $this->remoteAddress = $remoteAddress;
        $this->httpContext = $httpContext;
    }

    public function getCustomerIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

}
