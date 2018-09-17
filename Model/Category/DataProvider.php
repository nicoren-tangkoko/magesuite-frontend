<?php
namespace MageSuite\Frontend\Model\Category;

class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    protected function addUseDefaultSettings($category, $categoryData)
    {
        $data = parent::addUseDefaultSettings($category, $categoryData);

        if (isset($data['category_icon'])) {
            unset($data['category_icon']);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get('\MageSuite\Frontend\Helper\Category');

            $data['category_icon'][0]['name'] = $category->getData('category_icon');
            $data['category_icon'][0]['url']  	= $helper->getCategoryIcon($category);
        }

        if (isset($data['image_teaser'])) {
            unset($data['image_teaser']);

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $objectManager->get('\MageSuite\Frontend\Helper\Category');

            $data['image_teaser'][0]['name'] = $category->getData('image_teaser');
            $data['image_teaser'][0]['url'] = $helper->getImageTeaser($category);
        }

        return $data;
    }

    protected function getFieldsMap()
    {
        $fields = parent::getFieldsMap();
        $fields['content'][] = 'category_icon';

        return $fields;
    }

    public function getData()
    {
        $data = parent::getData();

        $params = $this->request->getParams();
        if (isset($params['id']) && isset($data[$params['id']]['category_view'])) {
            $data[$params['id']]['use_config']['category_view'] = false;
        }

        return $data;
    }

}