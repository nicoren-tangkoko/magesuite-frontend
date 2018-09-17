<?php

namespace MageSuite\Frontend\Plugin;

class CategoryView
{
    public function afterGetMode($subject, $result)
    {
        $category = $subject->getLayer()->getCurrentCategory();
        $view = $category->getCustomAttribute('category_view');
        $requestView = $subject->getRequest()->getParam('product_list_mode');
        if ($view !== null && $requestView === null) {
            return explode('-', $view->getValue())[0];
        }
        return $result;
    }
}
