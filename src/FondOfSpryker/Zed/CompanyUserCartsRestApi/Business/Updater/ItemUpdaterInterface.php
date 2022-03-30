<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface ItemUpdaterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return array<\Generated\Shared\Transfer\QuoteErrorTransfer>
     */
    public function update(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer): array;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return array<\Generated\Shared\Transfer\QuoteErrorTransfer>
     */
    public function updateMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): array;
}
