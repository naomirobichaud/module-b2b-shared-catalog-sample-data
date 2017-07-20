<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSharedCatalogSampleData\Model;


class TierPricing
{

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $group;

    /**
     * @var \Magento\SharedCatalog\Api\Data\SharedCatalogInterface
     */
    protected $sharedCatalog;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var array
     */
    protected $tierPricingCategoryPath = array('All Products/Lighting/LED Lamps');

    /**
     * @var string
     */
    protected $tierPricingGroup = 'Tools & Lighting';


    /**
     * TierPricing constructor.
     * @param \Magento\Catalog\Model\ProductFactory $product
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param \Magento\Customer\Model\Group $group
     * @param \Magento\SharedCatalog\Api\Data\SharedCatalogInterface $sharedCatalog
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(

        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Customer\Model\Group $group,
        \Magento\SharedCatalog\Api\Data\SharedCatalogInterface $sharedCatalog,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        $this->product = $product;
        $this->categoryCollection = $categoryCollection;
        $this->group = $group;
        $this->sharedCatalog = $sharedCatalog;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;

    }

    public function install()
    {
        $categoryPaths = $this->tierPricingCategoryPath;
        $tierCatIds = array();

        foreach ($categoryPaths as $categoryPath){
            array_push($tierCatIds,$this->getIdFromPath($this->_initCategories(),$categoryPath));
        }


        $custGroup = $this->getGroupIdFromName($this->tierPricingGroup);


        $tierProducts = $this->getProductsByCategoryIds($tierCatIds);
        foreach($tierProducts as $product){
            $productId = $product->getId();
            $tierProduct = $this->product->create();
            $tierProduct->load($productId);
            $tierPriceData = array(
                array ('website_id'=>0, 'cust_group'=>$custGroup, 'price_qty' => 10, 'percentage_value'=>10),
                array ('website_id'=>0, 'cust_group'=>$custGroup, 'price_qty' => 20, 'percentage_value'=>20)
            );
            $tierProduct->setData('tier_price', $tierPriceData);
            $tierProduct->save();

        }
    }

    /**
     * @param array $categoriesIds
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    private function getProductsByCategoryIds(array $categoriesIds)
    {
        /** @var \Magento\Framework\Api\SearchCriteria $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('category_id', $categoriesIds, 'in')
            ->create();
        return $this->productRepository->getList($searchCriteria)->getItems();
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getGroupIdFromName($name){
        $groupLoad = $this->group->load($name,'customer_group_code');
        return $groupLoad->getid();
    }

    /**
     * @param $categories
     * @param $string
     * @return bool
     */
    protected function getIdFromPath($categories,$string)
    {
        if (in_array($string, array_keys($categories))) {
            return $categories[$string];
        }

        return false;
    }

    /**\
     * @return array
     */
    protected function _initCategories()
    {
        $collection = $this->categoryCollection->addNameToResult();
        $categories = array();
        $categoriesWithRoots = array();
        foreach ($collection as $category) {
            $structure = explode('/', $category->getPath());
            $pathSize = count($structure);
            if ($pathSize > 1) {
                $path = array();
                for ($i = 1; $i < $pathSize; $i++) {
                    $path[] = $collection->getItemById($structure[$i])->getName();
                }
                $rootCategoryName = array_shift($path);
                if (!isset($categoriesWithRoots[$rootCategoryName])) {
                    $categoriesWithRoots[$rootCategoryName] = array();
                }
                $index = implode('/', $path);
                $categoriesWithRoots[$rootCategoryName][$index] = $category->getId();
                if ($pathSize > 2) {
                    $categories[$index] = $category->getId();
                }
            }
        }
        return $categories;
    }
}
