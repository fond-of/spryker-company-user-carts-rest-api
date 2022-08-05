<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;

interface ItemFinderInterface
{
    /**
     * @param array<string, ItemTransfer> $groupedItemTransfers
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    public function findInGroupedItemsByRestCartItem(
        array $groupedItemTransfers,
        RestCartItemTransfer $restCartItemTransfer
    ): ?ItemTransfer;
}
