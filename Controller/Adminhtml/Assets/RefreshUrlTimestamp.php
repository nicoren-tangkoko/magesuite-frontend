<?php

namespace MageSuite\Frontend\Controller\Adminhtml\Assets;

class RefreshUrlTimestamp extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $writer;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\Storage\WriterInterface $writer,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        parent::__construct($context);

        $this->writer = $writer;
        $this->messageManager = $messageManager;
    }

    public function execute()
    {
        try {
            $this->writer->save(
                \MageSuite\Frontend\Helper\Configuration::XML_PATH_ASSETS_URL_TIMESTAMP,
                time()
            );

            $this->messageManager->addSuccessMessage('New timestamp was generated correctly.');
        }
        catch(\Exception $e) {
            $this->messageManager->addErrorMessage(sprintf(
                'There was an error when trying to generate new timestamp: %s.',
                $e->getMessage()
            ));
        }

        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }
}
