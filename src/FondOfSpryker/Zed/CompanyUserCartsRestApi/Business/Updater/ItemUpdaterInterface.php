<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ItemUpdaterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function update(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer): QuoteResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function updateMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): QuoteResponseTransfer;
}
