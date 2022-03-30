<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover;

use Generated\Shared\Transfer\QuoteTransfer;

interface ItemRemoverInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return array<\Generated\Shared\Transfer\QuoteErrorTransfer>
     */
    public function removeMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): array;
}
