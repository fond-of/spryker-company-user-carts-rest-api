<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker;

use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;

interface WritePermissionCheckerInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return bool
     */
    public function checkByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): bool;
}
