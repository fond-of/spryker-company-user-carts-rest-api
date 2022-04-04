<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CompanyUserReferenceFilterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CustomerReferenceFilterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCartFilterInterface;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class RestCompanyUserCartsRequestMapper implements RestCompanyUserCartsRequestMapperInterface
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCartFilterInterface
     */
    protected $cartIdFilter;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CompanyUserReferenceFilterInterface
     */
    protected $companyUserReferenceFilter;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CustomerReferenceFilterInterface
     */
    protected $customerReferenceFilter;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCartFilterInterface $cartIdFilter
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CompanyUserReferenceFilterInterface $companyUserReferenceFilter
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CustomerReferenceFilterInterface $customerReferenceFilter
     */
    public function __construct(
        IdCartFilterInterface $cartIdFilter,
        CompanyUserReferenceFilterInterface $companyUserReferenceFilter,
        CustomerReferenceFilterInterface $customerReferenceFilter
    ) {
        $this->cartIdFilter = $cartIdFilter;
        $this->companyUserReferenceFilter = $companyUserReferenceFilter;
        $this->customerReferenceFilter = $customerReferenceFilter;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer
     */
    public function fromRestRequest(RestRequestInterface $restRequest): RestCompanyUserCartsRequestTransfer
    {
        return (new RestCompanyUserCartsRequestTransfer())
            ->setIdCart($this->cartIdFilter->filterFromRestRequest($restRequest))
            ->setCompanyUserReference($this->companyUserReferenceFilter->filterFromRestRequest($restRequest))
            ->setCustomerReference($this->customerReferenceFilter->filterFromRestRequest($restRequest));
    }
}
