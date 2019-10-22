<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface;
use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;

class CompanyUserCartsRestApiToCompanyUsersRestApiClientBridge implements CompanyUserCartsRestApiToCompanyUsersRestApiClientInterface
{
    /**
     * @var \FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface
     */
    protected $companyUsersRestApiClient;

    /**
     * @param \FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface $companyUsersRestApiClient
     */
    public function __construct(CompanyUsersRestApiClientInterface $companyUsersRestApiClient)
    {
        $this->companyUsersRestApiClient = $companyUsersRestApiClient;
    }

    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    public function findCompanyUserByCompanyUserReference(
        CompanyUserTransfer $companyUserTransfer
    ): CompanyUserResponseTransfer {
        return $this->companyUsersRestApiClient->findCompanyUserByCompanyUserReference($companyUserTransfer);
    }
}
