<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;

interface ItemFinderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    public function findInQuoteByRestCartItem(
        QuoteTransfer $quoteTransfer,
        RestCartItemTransfer $restCartItemTransfer
    ): ?ItemTransfer;
}
