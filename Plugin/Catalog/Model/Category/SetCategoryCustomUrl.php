<?php

namespace MageSuite\Frontend\Plugin\Catalog\Model\Category;

class SetCategoryCustomUrl
{
    /**
     * @var \MageSuite\Frontend\Helper\Category
     */
    protected $categoryHelper;

    /**
     * @var \MageSuite\ContentConstructorFrontend\Service\UrlResolver
     */
    protected $urlResolver;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \MageSuite\Frontend\Helper\Category $categoryHelper,
        \MageSuite\ContentConstructorFrontend\Service\UrlResolver $urlResolver,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->urlResolver = $urlResolver;
        $this->logger = $logger;
    }

    public function afterGetUrl(\Magento\Catalog\Model\Category $subject, $result)
    {
        $customUrl = $subject->getCategoryCustomUrl();

        if(empty($customUrl)){
            return $result;
        }

        try {
            $customUrl = $this->urlResolver->resolve($customUrl);
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                'Unable to process one or more directive attributes, error details: %s',
                $e->getMessage()
            ));
            return $result;
        }

        return $this->categoryHelper->prepareCategoryCustomUrl($customUrl);
    }
}
