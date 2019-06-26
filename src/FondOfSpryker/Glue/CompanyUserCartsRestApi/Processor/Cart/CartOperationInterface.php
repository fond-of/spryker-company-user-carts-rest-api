<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use Generated\Shared\Transfer\QuoteTransfer;

interface CartOperationInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    public function setQuoteTransfer(QuoteTransfer $quoteTransfer): self;

    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer[]|\ArrayObject $restCartItemTransfers
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    public function handleItems(ArrayObject $restCartItemTransfers): self;

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    public function reloadItems(): self;
}
