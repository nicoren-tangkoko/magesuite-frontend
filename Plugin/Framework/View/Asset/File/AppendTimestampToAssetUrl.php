<?php

namespace MageSuite\Frontend\Plugin\Framework\View\Asset\File;

class AppendTimestampToAssetUrl
{
    /**
     * @var int?
     */
    protected $timestamp = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \MageSuite\Frontend\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \MageSuite\Frontend\Helper\Configuration $configuration
    )
    {
        $this->logger = $logger;
        $this->configuration = $configuration;
    }

    public function afterGetUrl(\Magento\Framework\View\Asset\File $subject, $result)
    {
        if (!in_array($subject->getContentType(), ['js', 'css'])) {
            return $result;
        }

        $timestamp = $this->getTimestamp();

        if(empty($timestamp)) {
            return $result;
        }

        return sprintf(
            '%s?t=%d',
            $result,
            $timestamp
        );
    }

    protected function getTimestamp()
    {
        if($this->timestamp === null) {
            $this->timestamp = $this->configuration->getAssetsUrlTimestamp();
        }

        return $this->timestamp;
    }
}
