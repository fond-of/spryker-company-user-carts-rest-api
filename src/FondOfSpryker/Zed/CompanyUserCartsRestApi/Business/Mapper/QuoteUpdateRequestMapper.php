<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestAttributesTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;

class QuoteUpdateRequestMapper implements QuoteUpdateRequestMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteUpdateRequestTransfer
     */
    public function fromQuote(QuoteTransfer $quoteTransfer): QuoteUpdateRequestTransfer
    {
        $quoteUpdateRequestAttributesTransfer = (new QuoteUpdateRequestAttributesTransfer())
            ->fromArray($quoteTransfer->modifiedToArray(), true);

        return (new QuoteUpdateRequestTransfer())
            ->fromArray($quoteTransfer->modifiedToArray(), true)
            ->setCustomer($quoteTransfer->getCustomer())
            ->setIdQuote($quoteTransfer->getIdQuote())
            ->setQuoteUpdateRequestAttributes($quoteUpdateRequestAttributesTransfer);
    }
}
