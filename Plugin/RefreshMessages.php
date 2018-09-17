<?php

namespace MageSuite\Frontend\Plugin;

use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\Redirect;

class RefreshMessages
{
    const MESSAGES_COOKIES_NAME = 'mage-messages';

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\View\Element\Message\InterpretationStrategyInterface
     */
    private $interpretationStrategy;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $interpretationStrategy,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->appState = $context->getAppState();
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->messageManager = $messageManager;
        $this->jsonHelper = $jsonHelper;
        $this->interpretationStrategy = $interpretationStrategy;
    }

    protected function getMessages()
    {
        $messages = $this->getCookiesMessages();
        /** @var MessageInterface $message */
        foreach ($this->messageManager->getMessages(true)->getItems() as $message) {
            $messages[] = [
                'type' => $message->getType(),
                'text' => $this->interpretationStrategy->interpret($message),
            ];
        }
        return $messages;
    }

    protected function getCookiesMessages()
    {
        try {
            $messages = $this->jsonHelper->jsonDecode(
                $this->cookieManager->getCookie(self::MESSAGES_COOKIES_NAME, $this->jsonHelper->jsonEncode([]))
            );
            if (!is_array($messages)) {
                $messages = [];
            }
        } catch (\Zend_Json_Exception $e) {
            $messages = [];
        }
        return $messages;
    }

    public function refreshMessages()
    {
        $messages = $this->getMessages();

        if(!count($messages)){
            return;
        }

        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDurationOneYear();
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(false);
        $this->cookieManager->setPublicCookie(
            self::MESSAGES_COOKIES_NAME,
            $this->jsonHelper->jsonEncode($messages),
            $publicCookieMetadata
        );
    }

    public function afterDispatch($subject, $result)
    {
        if($this->appState->getAreaCode() == 'frontend' && !($result instanceof Json) && !($result instanceof Redirect)){
            $this->refreshMessages();
        }

        return $result;
    }
}