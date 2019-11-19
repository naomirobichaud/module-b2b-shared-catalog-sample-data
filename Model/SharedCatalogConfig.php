<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2bSharedCatalogSampleDataLight\Model;

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
     */
    protected $sharedCatalogName = 'Tools & Lighting';

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
     */
    protected $customCats = array('All Products/Lighting','All Products/Tools','Best Sellers');

    /**
     * @var array
     */
    protected $publicCats = array('All Products',
        'All Products/Home Automation & Controls',
        'All Products/Industrial Automation & Controls',
        'All Products/Lighting',
        'All Products/Power Distribution',
        'All Products/Switches & Wiring Devices',
        'All Products/Tools',
        'All Products/Home Automation & Controls/Accessories',
        'All Products/Home Automation & Controls/Audio',
        'All Products/Home Automation & Controls/Controls',
        'All Products/Home Automation & Controls/Video',
        'All Products/Industrial Automation & Controls/Control IEC',
        'All Products/Industrial Automation & Controls/Control Others',
        'All Products/Industrial Automation & Controls/Motor Control(MMC)',
        'All Products/Lighting/Ballast Lamps',
        'All Products/Lighting/CFL',
        'All Products/Lighting/Halogen Lamps',
        'All Products/Lighting/HID Lamps',
        'All Products/Lighting/Incandescent Lamps',
        'All Products/Lighting/LED Lamps',
        'All Products/Lighting/Linear Fluorescent Lamps',
        'All Products/Lighting/Recessed LEDs',
        'All Products/Lighting/Troffers',
        'All Products/Lighting/Wallpacks',
        'All Products/Power Distribution/Panels',
        'All Products/Power Distribution/Breakers',
        'All Products/Switches & Wiring Devices/GFCI',
        'All Products/Switches & Wiring Devices/Plugs & Receptacles',
        'All Products/Switches & Wiring Devices/Switches',
        'All Products/Switches & Wiring Devices/Wall Plates',
        'All Products/Tools/Clothing & Accessories',
        'All Products/Tools/Hand Tools',
        'All Products/Tools/Power Tools',
        'All Products/Tools/Shop Supplies','Best Sellers');

    protected $productCats = array('All Products','Best Sellers');

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
        /* add products to default catalog */
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