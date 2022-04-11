<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;

class ItemFinder implements ItemFinderInterface
{
    /**
     * @var array<string, array<string, \Generated\Shared\Transfer\ItemTransfer>>|null
     */
    protected $groupedItemTransfers;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    public function findInQuoteByRestCartItem(
        QuoteTransfer $quoteTransfer,
        RestCartItemTransfer $restCartItemTransfer
    ): ?ItemTransfer {
        $groupedItemTransfers = $this->getGroupedItemsByQuote($quoteTransfer);

        if (isset($groupedItemTransfers[$restCartItemTransfer->getGroupKey()])) {
            return $groupedItemTransfers[$restCartItemTransfer->getGroupKey()];
        }

        if (isset($groupedItemTransfers[$restCartItemTransfer->getSku()])) {
            return $groupedItemTransfers[$restCartItemTransfer->getSku()];
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<string, \Generated\Shared\Transfer\ItemTransfer>
     */
    protected function getGroupedItemsByQuote(QuoteTransfer $quoteTransfer): array
    {
        $key = sha1($quoteTransfer->serialize());

        if (isset($this->groupedItemTransfers[$key])) {
            return $this->groupedItemTransfers[$key];
        }

        $this->groupedItemTransfers[$key] = [];

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $searchKey = $itemTransfer->getGroupKey() ?? $itemTransfer->getSku();
            $this->groupedItemTransfers[$key][$searchKey] = $itemTransfer;
        }

        return $this->groupedItemTransfers[$key];
    }
}
