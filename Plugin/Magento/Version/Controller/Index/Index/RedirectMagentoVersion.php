<?php

declare(strict_types=1);

namespace MageSuite\Frontend\Plugin\Magento\Version\Controller\Index\Index;

class RedirectMagentoVersion
{
    public function afterExecute(\Magento\Version\Controller\Index\Index $subject, $result)
    {
        $result->setStatusHeader(404, '1.1', 'Not Found');
        $result->setContents('404 Not Found');

        return $result;
    }
}
