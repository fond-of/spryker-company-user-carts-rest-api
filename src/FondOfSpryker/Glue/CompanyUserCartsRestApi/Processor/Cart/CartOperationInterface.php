<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use Generated\Shared\Transfer\QuoteTransfer;

interface CartOperationInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return $this
     */
    public function setQuoteTransfer(QuoteTransfer $quoteTransfer): self;

    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer[]|\ArrayObject $restCartItemTransfers
     *
     * @return $this
     */
    public function handleItems(ArrayObject $restCartItemTransfers): self;
}
