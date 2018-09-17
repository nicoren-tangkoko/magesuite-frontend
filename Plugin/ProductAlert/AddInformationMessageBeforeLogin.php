<?php

namespace MageSuite\Frontend\Plugin\ProductAlert;

class AddInformationMessageBeforeLogin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Message\Manager
     */
    protected $messageManager;

    /**
     * AddInformationMessageBeforeLogin constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\AuthenticationStateInterface $authenticationState
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\Manager $messageManager
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
    }

    public function beforeDispatch(\Magento\Framework\App\ActionInterface $subject, \Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->customerSession->authenticate()) {
            $this->messageManager->addNotice(__('In order to use this feature please login or create an account.'));
        }
    }
}
