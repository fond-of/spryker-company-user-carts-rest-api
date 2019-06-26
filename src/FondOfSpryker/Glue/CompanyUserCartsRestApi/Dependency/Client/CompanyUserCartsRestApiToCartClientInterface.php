<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use Generated\Shared\Transfer\QuoteTransfer;

interface CompanyUserCartsRestApiToCartClientInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     * @param array $params
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addItems(array $itemTransfers, array $params = []): QuoteTransfer;

    /**
     * @return void
     */
    public function reloadItems(): void;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function removeItems(array $itemTransfers): QuoteTransfer;
}
