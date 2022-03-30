<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business;

use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\CompanyUserCartsRestApiBusinessFactory getFactory()
 */
class CompanyUserCartsRestApiFacade extends AbstractFacade implements CompanyUserCartsRestApiFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function updateQuoteByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        return $this->getFactory()
            ->createQuoteUpdater()
            ->updateByRestCompanyUserCartsRequest($restCompanyUserCartsRequestTransfer);
    }
}
