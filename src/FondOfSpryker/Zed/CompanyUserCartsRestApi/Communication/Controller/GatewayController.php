<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Communication\Controller;

use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\CompanyUserCartsRestApiFacadeInterface getFacade()
 */
class GatewayController extends AbstractGatewayController
{
/**
 * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
 *
 * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
 */
    public function updateQuoteByRestCompanyUserCartsRequestAction(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        return $this->getFacade()->updateQuoteByRestCompanyUserCartsRequest($restCompanyUserCartsRequestTransfer);
    }
}
