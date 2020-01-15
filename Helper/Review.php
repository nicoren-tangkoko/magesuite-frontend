<?php

namespace MageSuite\Frontend\Helper;

class Review extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MAX_STARS_VALUE = 5;

    /**
     * @var \Magento\Review\Model\Review
     */
    protected $review;

    /**
     * @var \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection
     */
    protected $voteCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Review\Model\ResourceModel\Rating\CollectionFactory
     */
    protected $ratingCollectionFactory;

    /**
     * @var \Magento\Review\Model\Rating[]|null
     */
    protected $ratings = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Review\Model\Review $review,
        \Magento\Review\Model\ResourceModel\Rating\Option\Vote\Collection $voteCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ResourceModel\Rating\CollectionFactory $ratingCollectionFactory
    )
    {
        parent::__construct($context);

        $this->review = $review;
        $this->voteCollection = $voteCollection;
        $this->storeManager = $storeManager;
        $this->ratingCollectionFactory = $ratingCollectionFactory;
    }

    public function getReviewSummary($product, $includeVotes = false)
    {
        $reviewData = [];

        if ($product) {
            $storeId = $this->storeManager->getStore()->getId();
            $ratingSummary = $product->getRatingSummary();
            $reviewsCount = $product->getReviewsCount();

            if (!$ratingSummary) {
                $this->review->getEntitySummary($product, $storeId);
                $ratingSummary = $product->getRatingSummary();
            }
            // Since 2.3.3 rating summary is being returned directly, not as an object.
            if (is_object($ratingSummary)) {
                $reviewsCount = $ratingSummary->getReviewsCount();
                $ratingSummary = $ratingSummary->getRatingSummary();
            }

            if ($ratingSummary) {
                $activeStars = $ratingSummary ? $this->getStarsAmount($ratingSummary) : 0;

                $reviewData = [
                    'data' => [
                        'maxStars' => self::MAX_STARS_VALUE,
                        'activeStars' => $activeStars,
                        'count' => $reviewsCount,
                        'votes' => array_fill(1, self::MAX_STARS_VALUE, 0),
                        'ratings' => []
                    ]
                ];

                if ($includeVotes and $reviewData['data']['count']) {
                    $reviewData = $this->prepareAdditionalRatingData($reviewData, $product->getId(), $storeId);
                }
            }
        }

        return $reviewData;
    }

    protected function prepareAdditionalRatingData($reviewData, $productId, $storeId)
    {
        $votes = $this->voteCollection
            ->setEntityPkFilter($productId)
            ->setStoreFilter($storeId);

        $groupedVotes = [
            'review' => [],
            'rating' => []
        ];

        foreach ($votes->getItems() AS $vote) {
            $vote->getData();
            $groupedVotes['review'][$vote->getReviewId()][] = $vote->getPercent();
            $groupedVotes['rating'][$vote->getRatingId()][] = $vote->getPercent();
        }

        $ratings = $this->getRatings();

        foreach($groupedVotes as $type => $group){
            foreach ($group as $typeId => $votes){
                $starsAmount = $this->getStarsAmount($votes);

                if($type == 'review'){
                    $reviewData['data']['votes'][(int)$starsAmount]++;
                }elseif($type == 'rating'){
                    $reviewData['data']['ratings'][$typeId]['starsAmount'] = $starsAmount;
                    $reviewData['data']['ratings'][$typeId]['label'] = isset($ratings[$typeId]) ? $ratings[$typeId]->getRatingCode() : null;
                }
            }
        }

        return $reviewData;
    }

    protected function getStarsAmount($value)
    {
        if(is_array($value)){
            $value = array_sum($value) / count($value);
        }

        return round($value / 10) / 2;
    }

    /**
     * @return \Magento\Review\Model\Rating[]|null
     */
    public function getRatings() {
        if($this->ratings == null) {
            $storeId = $this->storeManager->getStore()->getId();

            $ratings = $this->ratingCollectionFactory->create()
                ->addEntityFilter('product')
                ->setPositionOrder()
                ->setStoreFilter($storeId)
                ->addRatingPerStoreName($storeId)
                ->load();

            /** @var \Magento\Review\Model\Rating $rating */
            foreach($ratings as $rating) {
                $this->ratings[$rating->getId()] = $rating;
            }
        }

        return $this->ratings;
    }
}