<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\B2BSharedCatalogSampleData\Setup;

use Magento\Framework\Setup;


class Installer implements Setup\SampleData\InstallerInterface
{

    protected $companySetup;
    protected $customerSetup;
    protected $salesrepSetup;
    protected $teamSetup;
    protected $catalogSetup;
    protected $sharedCatalogConfig;
    protected $tierPricing;
    protected $relatedProducts;
    protected $sampleOrder;
    protected $index;


    public function __construct(
        \MagentoEse\B2BSharedCatalogSampleData\Model\CompanyCatalog $catalogSetup,
        \MagentoEse\B2BSharedCatalogSampleData\Model\SharedCatalogConfig $sharedCatalogConfig,
        \MagentoEse\B2BSharedCatalogSampleData\Model\TierPricing $tierPricing,
        \MagentoEse\B2BSharedCatalogSampleData\Model\PreferredProducts $preferredProducts,
        \MagentoEse\B2BSharedCatalogSampleData\Model\Related $relatedProducts

    ) {

        $this->catalogSetup = $catalogSetup;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->tierPricing = $tierPricing;
        $this->preferredProducts = $preferredProducts;
        $this->relatedProducts = $relatedProducts;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->catalogSetup->install();
        echo ("catalogSetup\n");
        $this->relatedProducts->install(['MagentoEse_B2BSampleData::fixtures/related_products.csv']);
        echo ("relatedProducts\n");
        //$this->index->reindexAll();
        $this->sharedCatalogConfig->install();
        echo ("sharedCatalogConfig\n");
        $this->preferredProducts->install(['MagentoEse_B2BSampleData::fixtures/preferredproducts.csv']);
        echo ("preferredProducts\n");
        $this->tierPricing->install();
        echo ("tierPricing\n");


    }
}