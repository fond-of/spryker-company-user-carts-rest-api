<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface;
use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;

class CompanyUserCartsRestApiToCompanyUserReferenceClientBridge implements CompanyUserCartsRestApiToCompanyUserReferenceClientInterface
{
    /**
     * @var \FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface
     */
    protected $companyUserReferenceClient;

    /**
     * @param \FondOfSpryker\Client\CompanyUserReference\CompanyUserReferenceClientInterface $companyUserReferenceClient
     */
    public function __construct(CompanyUserReferenceClientInterface $companyUserReferenceClient)
    {
        $this->companyUserReferenceClient = $companyUserReferenceClient;
    }

    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    public function findCompanyUserByCompanyUserReference(
        CompanyUserTransfer $companyUserTransfer
    ): CompanyUserResponseTransfer {
        return $this->companyUserReferenceClient->findCompanyUserByCompanyUserReference($companyUserTransfer);
    }
}
