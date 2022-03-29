<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder;

use Generated\Shared\Transfer\QuoteTransfer;

interface ItemAdderInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return array<\Generated\Shared\Transfer\QuoteErrorTransfer>
     */
    public function addMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): array;
}
