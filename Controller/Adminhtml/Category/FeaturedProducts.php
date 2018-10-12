<?php

namespace MageSuite\Frontend\Controller\Adminhtml\Category;


abstract class FeaturedProducts extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageSuite_Frontend::item_list';


}