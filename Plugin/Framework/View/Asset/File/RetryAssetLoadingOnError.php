<?php

namespace MageSuite\Frontend\Plugin\Framework\View\Asset\File;

class RetryAssetLoadingOnError
{
    const WAIT_BETWEEN_RETRY_IN_MILISECONDS = 50;
    const AMOUNT_OF_RETRIES = 10;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }

    public function aroundGetContent(\Magento\Framework\View\Asset\File $subject, callable $proceed) {
       $attempt = 1;

       do {
           try {
               $content = $proceed();

               if(!empty($content)) {
                   return $content;
               }

               $this->logLastError($attempt, $subject->getPath());
               usleep(self::WAIT_BETWEEN_RETRY_IN_MILISECONDS*1000);
               $attempt++;
           }
           catch(\Magento\Framework\View\Asset\File\NotFoundException $e) {
               throw $e;
           }
           catch(\Exception $e) {
               $this->logger->error(sprintf(
                   'Unable to load source file "%s" for merging during %d attempt, error details: %s, %s',
                   $subject->getPath(),
                   $attempt,
                   $e->getMessage(),
                   $e->getTraceAsString()
               ));

               usleep(self::WAIT_BETWEEN_RETRY_IN_MILISECONDS*1000);
               $attempt++;
           }
       }
       while($attempt < self::AMOUNT_OF_RETRIES);

       return '';
    }

    protected function logLastError($attempt, $path)
    {
        $error = error_get_last();

        if($error === null || !is_array($error)) {
            return;
        }

        $this->logger->error(sprintf(
            'Unable to load source file "%s" for merging during %d attempt, error details: %s',
            $path,
            $attempt,
            json_encode($error)
        ));
    }

}
