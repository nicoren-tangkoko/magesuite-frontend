<?php

namespace MageSuite\Frontend\Plugin;

class UpdateCategoryImagesData
{
    /*
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    private $fileInfo = null;

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    public function afterGetData($subject, $result)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.3.0') >= 0) {
            return $result;
        }

        $categoryIds = array_keys($result);

        foreach($categoryIds AS $categoryId){
            $categoryData = $result[$categoryId];

            if(isset($categoryData['image']) AND $categoryData['image'] AND !isset($categoryData['image'][0]['name'])){
                $result[$categoryId]['image'] = null;
            }

            if(
                isset($categoryData['category_icon'])
                AND $categoryData['category_icon']
                AND isset($categoryData['category_icon'][0]['name'])
                AND !isset($categoryData['category_icon'][0]['size'])
            ){
                $fileName = $categoryData['category_icon'][0]['name'];
                if($this->getFileInfo()->isExist($fileName)){
                    $stat = $this->getFileInfo()->getStat($categoryData['category_icon'][0]['name']);
                    $mime = $this->getFileInfo()->getMimeType($categoryData['category_icon'][0]['name']);

                    $result[$categoryId]['category_icon'][0]['size'] = isset($stat) ? $stat['size'] : 0;
                    $result[$categoryId]['category_icon'][0]['type'] = $mime;
                }
            }
        }

        return $result;
    }

    private function getFileInfo()
    {
        if ($this->fileInfo === null) {
            $this->fileInfo = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Catalog\Model\Category\FileInfo::class);
        }
        return $this->fileInfo;
    }
}