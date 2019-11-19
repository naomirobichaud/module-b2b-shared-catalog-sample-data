<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MagentoEse\B2bSharedCatalogSampleDataLight\Model;

//use Magento\Framework\Setup\SampleData\Context as SampleDataContext;


class CompanyCatalog
{

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Company\Model\CompanyRepository
     */
    protected $companyRepository;

    /**
     * @var \Magento\SharedCatalog\Model\SharedCatalogFactory
     */
    protected $sharedCatalog;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $customerGroup;

    /**
     * @var \Magento\Company\Model\ResourceModel\Customer
     */
    protected $companyCustomer;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $group;

    /**
     * @var \Magento\SharedCatalog\Model\Management
     */
    protected $management;

    /**
     * @var \Magento\SharedCatalog\Model\CategoryManagement
     */
    protected $categoryManagement;

    /**
     * @var \Magento\SharedCatalog\Model\Repository
     */
    protected $sharedCatalogRepository;

    /**
     * @var string
     */
    protected $companyWithCatalog = 'Vandelay Industries';

    /**
     * @var string
     */
    protected $sharedCatalogGroupCode = 'Tools & Lighting';

    /**
     * @var string
     */
    protected $validCompanyGroupCode = 'B2B Registered Users';

    /**
     * @var array
     */
    protected $nonSharedCatalogCompanies = array('Dunder Mifflin');

    /**
     * CompanyCatalog constructor.
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Company\Model\CompanyRepository $companyRepository
     * @param \Magento\SharedCatalog\Model\SharedCatalogFactory $sharedCatalog
     * @param \Magento\Customer\Model\GroupFactory $customerGroup
     * @param \Magento\Company\Model\ResourceModel\Customer $companyCustomer
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Group $group
     * @param \Magento\SharedCatalog\Model\Repository $sharedCatalogRepository
     * @param \Magento\SharedCatalog\Model\Management $management
     * @param \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement
     */
    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Company\Model\CompanyRepository $companyRepository,
        \Magento\SharedCatalog\Model\SharedCatalogFactory $sharedCatalog,
        \Magento\Customer\Model\GroupFactory $customerGroup,
        \Magento\Company\Model\ResourceModel\Customer $companyCustomer,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Group $group,
        \Magento\SharedCatalog\Model\Repository $sharedCatalogRepository,
        \Magento\SharedCatalog\Model\Management $management,
        \Magento\SharedCatalog\Model\CategoryManagement $categoryManagement
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyRepository = $companyRepository;
        $this->sharedCatalog = $sharedCatalog;
        $this->customerGroup = $customerGroup;
        $this->companyCustomer = $companyCustomer;
        $this->customer = $customer;
        $this->group = $group;
        $this->sharedCatalogRepository = $sharedCatalogRepository;
        $this->management = $management;
        $this->categoryManagement = $categoryManagement;
    }

    public function install()
    {

        //get company
        $company = $this->getCompany($this->companyWithCatalog);
        //create group
        $groupAttribute = $this->customerGroup->create();
        $groupAttribute->setCode($this->sharedCatalogGroupCode);
        //$groupAttribute->set
        $groupAttribute->save();
        $groupId = $groupAttribute->getId();
        //create catalog
        $catalogData = array(
            "name" => $this->sharedCatalogGroupCode,
            "description" => "",
            "customer_group_id" => $groupId
        );
        $this->createCatalog($catalogData);
        //attach group to company
        $company->setCustomerGroupId($groupId);
        $company->save();
        //get customers attached to the company
        $companyCustomers = $this->companyCustomer->getCustomerIdsByCompanyId($company->getId());
        //set customers group to shared catalog
        foreach ($companyCustomers as $customerId) {
            $cust = $this->customer->load($customerId);
            $cust->setGroupId($groupId);
            $cust->save();
        }
        // create catalog for logged in users


        //create group
        $groupAttribute = $this->customerGroup->create();
        $groupAttribute->setCode($this->validCompanyGroupCode);
        //$groupAttribute->set
        $groupAttribute->save();
        $groupId = $groupAttribute->getId();
        //create catalog
        $catalogData = array(
            "name" => $this->validCompanyGroupCode,
            "description" => "",
            "customer_group_id" => $groupId
        );
        $this->createCatalog($catalogData);

        //get company
        foreach ($this->nonSharedCatalogCompanies as $nonSharedCatalogCompany){
            $company = $this->getCompany($nonSharedCatalogCompany);

            //attach group to company
            $company->setCustomerGroupId($groupId);
            $company->save();
            //get customers attached to the company
            $companyCustomers = $this->companyCustomer->getCustomerIdsByCompanyId($company->getId());
            //set customers group to shared catalog
            foreach ($companyCustomers as $customerId) {
                $cust = $this->customer->load($customerId);
                $cust->setGroupId($groupId);
                $cust->save();
            }
        }
    }

    /**
     * @param string $companyName
     * @return \Magento\Company\Api\Data\CompanyInterface|mixed
     */
    private function getCompany($companyName){
        $catalogFilter = $this->searchCriteriaBuilder;
        $catalogFilter->addFilter('company_name',$companyName);
        $companyList = $this->companyRepository->getList($catalogFilter->create())->getItems();
        return reset($companyList);
    }

    /**
     * @param array $catalogData
     * @return \Magento\SharedCatalog\Model\SharedCatalog
     */
    private function createCatalog(array $catalogData){
        /** @var \Magento\SharedCatalog\Model\SharedCatalog $catalog */
        $catalog = $this->sharedCatalog->create();
        $catalog->setName($catalogData['name']);
        $catalog->setDescription($catalogData['description']);
        $catalog->setType(0); //0 is custom, 1 is public
        $catalog->setCreatedBy(1);//admin user id
        $catalog->setStoreId(0);
        $catalog->setCustomerGroupId($catalogData['customer_group_id']);
        $catalog->save();
        return $catalog;
    }
 }
