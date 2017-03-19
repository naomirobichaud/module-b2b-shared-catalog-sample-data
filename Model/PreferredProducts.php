<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSharedCatalogSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;



class PreferredProducts
{


    protected $sampleDataContext;
    protected $product;

    /**
     * @var \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    protected $resourceConnection;


    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
     */
    protected $categoryCollection;


    public function __construct(
        SampleDataContext $sampleDataContext,
       \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection
    )
    {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->product = $product;
        $this->resourceConnection = $resourceConnection;
        $this->categoryCollection = $categoryCollection;


    }

    public function install(array $fixtures)
    {
        $preferredCategories = array('All Products/Tools','All Products/Tools/Power Tools');
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);
            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                //set tier prices
                $tierProduct = $this->product->create();
                $tierProduct->load($tierProduct->getIdBySku($data['sku']));
                $orgPrice = $tierProduct->getPrice();
                $tierPriceData = array(
                    array ('website_id'=>0, 'cust_group'=>4, 'price_qty' => 10, 'percentage_value'=>10),
                    array ('website_id'=>0, 'cust_group'=>4, 'price_qty' => 20, 'percentage_value'=>20),
                    array ('website_id'=>0, 'cust_group'=>5, 'price_qty' => 10, 'percentage_value'=>10),
                    array ('website_id'=>0, 'cust_group'=>5, 'price_qty' => 20, 'percentage_value'=>20)
                );
                $tierProduct->setData('tier_price', $tierPriceData);
                $tierProduct->save();
                //set product position to zero
                foreach($preferredCategories as $preferredCategory) {
                    $categoryIds[] = $this->getIdFromPath($this->_initCategories(), $preferredCategory);
                    foreach ($categoryIds as $categoryId) {
                        $productId = $tierProduct->getId();
                        $this->updateProductPosition($categoryId, $productId, 0);
                    }
                }

            }

        }
        $this->__destruct();
    }


    private function updateProductPosition($categoryId,$productId,$position){
        //this is not the proper method, but was done in interest of deadline
        $connection = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('catalog_category_product');
        $sql = "update " . $tableName . " set position = ".$position." where category_id = ".$categoryId." and product_id=".$productId;
        $connection->query($sql);
    }

    protected function getIdFromPath($categories,$string)
    {
        if (in_array($string, array_keys($categories))) {
            return $categories[$string];
        }

        return false;
    }

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



    public function __destruct(){
        $this->fixtureManager = null;
        $this->csvReader = null;
        $this->product = null;
    }

}
