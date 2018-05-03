<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MagentoEse\B2BSharedCatalogSampleData\Setup;

use Magento\Framework\Setup;


class Installer implements Setup\SampleData\InstallerInterface
{

    /**
     * @var \MagentoEse\B2BSharedCatalogSampleData\Model\CompanyCatalog
     */
    protected $catalogSetup;

    /**
     * @var \MagentoEse\B2BSharedCatalogSampleData\Model\SharedCatalogConfig
     */
    protected $sharedCatalogConfig;

    /**
     * @var \MagentoEse\B2BSharedCatalogSampleData\Model\TierPricing
     */
    protected $tierPricing;

    /**
     * @var \MagentoEse\B2BSharedCatalogSampleData\Model\Related
     */
    protected $relatedProducts;

    /**
     * @var \MagentoEse\B2BSharedCatalogSampleData\Model\PreferredProducts
     */
    protected $preferredProducts;


    /**
     * Installer constructor.
     * @param \MagentoEse\B2BSharedCatalogSampleData\Model\CompanyCatalog $catalogSetup
     * @param \MagentoEse\B2BSharedCatalogSampleData\Model\SharedCatalogConfig $sharedCatalogConfig
     * @param \MagentoEse\B2BSharedCatalogSampleData\Model\TierPricing $tierPricing
     * @param \MagentoEse\B2BSharedCatalogSampleData\Model\PreferredProducts $preferredProducts
     * @param \MagentoEse\B2BSharedCatalogSampleData\Model\Related $relatedProducts
     */
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
    {   $this->catalogSetup->install();
        $this->relatedProducts->install(['MagentoEse_B2BSharedCatalogSampleData::fixtures/related_products.csv']);
        $this->sharedCatalogConfig->install();
        $this->preferredProducts->install(['MagentoEse_B2BSharedCatalogSampleData::fixtures/preferredproducts.csv']);
        $this->tierPricing->install([
            'MagentoEse_B2BSharedCatalogSampleData::fixtures/legrand_tier_pricing.csv',
            'MagentoEse_B2BSharedCatalogSampleData::fixtures/milwaukee_tier_pricing.csv',
            'MagentoEse_B2BSharedCatalogSampleData::fixtures/philips_tier_pricing.csv',
            'MagentoEse_B2BSharedCatalogSampleData::fixtures/siemens_tier_pricing.csv',
            'MagentoEse_B2BSharedCatalogSampleData::fixtures/case_tier_pricing.csv']);
    }
}