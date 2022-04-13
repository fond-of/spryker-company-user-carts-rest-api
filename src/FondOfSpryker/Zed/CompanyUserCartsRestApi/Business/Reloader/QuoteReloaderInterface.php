<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

interface QuoteReloaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function reload(QuoteTransfer $quoteTransfer): RestCompanyUserCartsResponseTransfer;
}
