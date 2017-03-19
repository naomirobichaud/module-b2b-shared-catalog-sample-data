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
        \MagentoEse\B2BSharedCatalogSampleData\Model\Related $relatedProducts,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {

        $this->catalogSetup = $catalogSetup;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
        $this->tierPricing = $tierPricing;
        $this->preferredProducts = $preferredProducts;
        $this->relatedProducts = $relatedProducts;
        $this->scopeConfig = $scopeConfig;
    }


    /**
     * {@inheritdoc}
     */
    public function install()
    {   $this->catalogSetup->install();
        $this->relatedProducts->install(['MagentoEse_B2BSharedCatalogSampleData::fixtures/related_products.csv']);
        $this->sharedCatalogConfig->install();
        $this->preferredProducts->install(['MagentoEse_B2BSharedCatalogSampleData::fixtures/preferredproducts.csv']);
        $this->tierPricing->install();
    }
}