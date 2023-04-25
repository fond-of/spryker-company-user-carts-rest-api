<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Communication\Plugin\PermissionExtension\ReadCompanyUserCartPermissionPlugin;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;

class ReadPermissionChecker implements ReadPermissionCheckerInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface
     */
    protected CompanyUserReaderInterface $companyUserReader;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface
     */
    protected CompanyUserCartsRestApiToPermissionFacadeInterface $permissionFacade;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface $companyUserReader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface $permissionFacade
     */
    public function __construct(
        CompanyUserReaderInterface $companyUserReader,
        CompanyUserCartsRestApiToPermissionFacadeInterface $permissionFacade
    ) {
        $this->companyUserReader = $companyUserReader;
        $this->permissionFacade = $permissionFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return bool
     */
    public function checkByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): bool {
        $idCompanyUser = $this->companyUserReader->getIdByRestCompanyUserCartsRequest(
            $restCompanyUserCartsRequestTransfer,
        );

        if ($idCompanyUser === null) {
            return false;
        }

        return $this->checkByIdCompanyUser($idCompanyUser);
    }

    /**
     * @param int $idCompanyUser
     *
     * @return bool
     */
    public function checkByIdCompanyUser(int $idCompanyUser): bool
    {
        return $this->permissionFacade->can(ReadCompanyUserCartPermissionPlugin::KEY, $idCompanyUser);
    }
}
