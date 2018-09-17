<?php

namespace MageSuite\Frontend\Plugin\Wishlist;

class AddInformationMessageBeforeLogin
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Wishlist\Model\AuthenticationStateInterface
     */
    protected $authenticationState;

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
        \Magento\Wishlist\Model\AuthenticationStateInterface $authenticationState,
        \Magento\Framework\Message\Manager $messageManager
    ) {
        $this->customerSession = $customerSession;
        $this->authenticationState = $authenticationState;
        $this->messageManager = $messageManager;
    }

    public function beforeDispatch(\Magento\Framework\App\ActionInterface $subject, \Magento\Framework\App\RequestInterface $request)
    {
        if ($this->authenticationState->isEnabled() && !$this->customerSession->authenticate()) {
            $this->messageManager->addNotice(__('In order to use this feature please login or create an account.'));
        }
    }
}
