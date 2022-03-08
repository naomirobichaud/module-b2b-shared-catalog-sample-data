<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSharedCatalogSampleData\Model;

class SharedCatalogConfig {

    /**
     * @var \Magento\SharedCatalog\Api\SharedCatalogRepositoryInterface
     */
    protected $sharedCatalogRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollection;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var string
     * Edited to appropriote catalog name for Venia. 
     */
    protected $sharedCatalogName = 'All Products Shared Catalog';

    /**
     * @var string
     */
    protected $validCatalogName = 'B2B Registered Users';

    /**
     * @var string
     */
    protected $publicCatalogName = 'Default (General)';

    /**
     * @var array
     * Corresponds to sharedCatalogName
     */
    protected $customCats = array('Accessories','Accessories/Belts','Accessories/Jewelry' 'Accessories/Scarves');

    /**
     * @var array
     */
    protected $publicCats = array('Accessories','Accessories/Belts');

    protected $productCats = array('Accessories/Scarves');

    /** @var \Magento\SharedCatalog\Model\SharedCatalogAssignment  */
    protected $sharedCatalogAssignment;

    /**
     * SharedCatalogConfig constructor.
     * @param \Magento\SharedCatalog\Api\SharedCatalogRepositoryInterface $sharedCatalogRepository
     * @param \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\SharedCatalog\Model\SharedCatalogAssignment $sharedCatalogAssignment
     * @param \Magento\SharedCatalog\Api\CategoryManagementInterface $categoryManagement
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryFactory
     */
    public function __construct(
        \Magento\SharedCatalog\Api\SharedCatalogRepositoryInterface $sharedCatalogRepository,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\SharedCatalog\Model\SharedCatalogAssignment $sharedCatalogAssignment,
        \Magento\SharedCatalog\Api\CategoryManagementInterface $categoryManagement,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryFactory
    )
    {
        $this->sharedCatalogRepository = $sharedCatalogRepository;
        $this->categoryCollection = $categoryCollection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sharedCatalogAssignment = $sharedCatalogAssignment;
        $this->categoryManagement = $categoryManagement;
        $this->categoryFactory = $categoryFactory;
    }

    public function install(){

        
        /* add products to custom catalog */
        $this->assignProductsToCatalog($this->sharedCatalogName, $this->customCats);
        $this->assignProductsToCatalogCategories($this->sharedCatalogName, $this->customCats);
        $this->assignProductsToCatalog($this->validCatalogName, $this->publicCats);
        $this->assignProductsToCatalogCategories($this->validCatalogName, $this->publicCats);
        /* add products to default catalog - why is productCats used here?? */
        $this->assignProductsToCatalog($this->publicCatalogName, $this->publicCats);
        $this->assignProductsToCatalogCategories($this->publicCatalogName, $this->productCats);
    }

    /**
     * @param string $catalogName
     * @param array $categoryPaths
     */
    private function assignProductsToCatalog($catalogName, array $categoryPaths)
    {
        $customCatIds = array();
        foreach ($categoryPaths as $categoryPath){
            $customCatIds = [$this->getIdFromPath($this->_initCategories(),$categoryPath)];
            //get catalog id
            $catalogId = $this->getCatalogByName($catalogName)->getid();
            //assign categories to catalog
            $this->categoryManagement->assignCategories($catalogId,$this->getCategories($customCatIds));
            //assign to catalog
            //$this->sharedCatalogAssignment->assignProductsForCategories($catalogId,$customCatIds);
        }

    }

    /**
     * @param string $catalogName
     * @param array $categoryPaths
     */
    private function assignProductsToCatalogCategories($catalogName, array $categoryPaths)
    {
        $customCatIds = array();
        foreach ($categoryPaths as $categoryPath){
            $customCatIds = [$this->getIdFromPath($this->_initCategories(),$categoryPath)];
            //get catalog id
            $catalogId = $this->getCatalogByName($catalogName)->getid();
            //assign categories to catalog
            //$this->categoryManagement->assignCategories($catalogId,$this->getCategories($customCatIds));
            //assign to catalog
            $this->sharedCatalogAssignment->assignProductsForCategories($catalogId,$customCatIds);
        }

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

    /**
     * @param $categoryIds
     * @return array
     */
    protected function getCategories($categoryIds){
        $categories = [];
        foreach($categoryIds as $categoryId){
            $category = $this->categoryFactory->get($categoryId);
           array_push($categories,$category);
        }
        return $categories;
    }

    /**
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

    /**
     * @param $catalogName
     * @return \Magento\SharedCatalog\Api\Data\SharedCatalogInterface|mixed
     */
    protected function getCatalogByName($catalogName){
        $catalogFilter = $this->searchCriteriaBuilder;
        $catalogFilter->addFilter('name',$catalogName);
        $catalogList = $this->sharedCatalogRepository->getList($catalogFilter->create())->getItems();
        return reset($catalogList);
    }
}
