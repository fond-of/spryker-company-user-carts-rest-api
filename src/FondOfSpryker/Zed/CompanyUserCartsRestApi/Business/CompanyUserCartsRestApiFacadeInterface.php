<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business;

use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

interface CompanyUserCartsRestApiFacadeInterface
{
    /**
     * Specifications:
     * - Updates quote data like name, ...
     * - Adds quote items
     * - Remove quote items
     * - Update quote items
     * - ...
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function updateQuoteByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer;
}
