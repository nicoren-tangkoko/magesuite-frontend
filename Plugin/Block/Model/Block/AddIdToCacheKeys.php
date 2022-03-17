<?php

namespace MageSuite\Frontend\Plugin\Block\Model\Block;

class AddIdToCacheKeys
{
    public function afterGetCacheTags(\Magento\Cms\Model\Block $subject, $result) {
        if(!is_array($result) && is_string($result)) {
            $result = [$result];
        }

        $result[] = sprintf('cms_b_%s', $subject->getBlockId());

        return $result;
    }
}
