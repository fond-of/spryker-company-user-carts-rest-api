<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade;

use FondOfSpryker\Zed\CompanyUserReference\Business\CompanyUserReferenceFacadeInterface;
use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;

class CompanyUserCartsRestApiToCompanyUserReferenceFacadeBridge implements CompanyUserCartsRestApiToCompanyUserReferenceFacadeInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserReference\Business\CompanyUserReferenceFacadeInterface
     */
    protected CompanyUserReferenceFacadeInterface $companyUserReferenceFacade;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserReference\Business\CompanyUserReferenceFacadeInterface $companyUserReferenceFacade
     */
    public function __construct(
        CompanyUserReferenceFacadeInterface $companyUserReferenceFacade
    ) {
        $this->companyUserReferenceFacade = $companyUserReferenceFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    public function findCompanyUserByCompanyUserReference(CompanyUserTransfer $companyUserTransfer): CompanyUserResponseTransfer
    {
        return $this->companyUserReferenceFacade->findCompanyUserByCompanyUserReference($companyUserTransfer);
    }

    /**
     * @param string $companyUserReference
     * @param int $idCustomer
     *
     * @return int|null
     */
    public function getIdCompanyUserByCompanyUserReferenceAndIdCustomer(string $companyUserReference, int $idCustomer): ?int
    {
        return $this->companyUserReferenceFacade->getIdCompanyUserByCompanyUserReferenceAndIdCustomer(
            $companyUserReference,
            $idCustomer,
        );
    }
}
