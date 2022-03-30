<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader;

use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteReloaderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function reload(QuoteTransfer $quoteTransfer): QuoteResponseTransfer;
}
