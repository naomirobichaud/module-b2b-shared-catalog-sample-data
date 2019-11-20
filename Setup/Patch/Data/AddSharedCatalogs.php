<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2BSharedCatalogSampleData\Setup\Patch\Data;


use Magento\Framework\Setup\Patch\DataPatchInterface;
use MagentoEse\B2BSharedCatalogSampleData\Model\CompanyCatalog;
use MagentoEse\B2BSharedCatalogSampleData\Model\SharedCatalogConfig;

class AddSharedCatalogs implements DataPatchInterface
{

    /** @var CompanyCatalog  */
    protected $companyCatalog;

    /** @var SharedCatalogConfig  */
    protected $sharedCatalogConfig;

    public function __construct(CompanyCatalog $companyCatalog, SharedCatalogConfig $sharedCatalogConfig)
    {
        $this->companyCatalog = $companyCatalog;
        $this->sharedCatalogConfig = $sharedCatalogConfig;
    }

    public function apply()
    {
        $this->companyCatalog->install();
        $this->sharedCatalogConfig->install();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}