<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;

interface QuoteUpdateRequestMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteUpdateRequestTransfer
     */
    public function fromQuote(QuoteTransfer $quoteTransfer): QuoteUpdateRequestTransfer;
}
