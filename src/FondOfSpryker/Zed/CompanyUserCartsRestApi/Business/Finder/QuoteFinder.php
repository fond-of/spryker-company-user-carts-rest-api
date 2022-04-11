<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder;

use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

class QuoteFinder implements QuoteFinderInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface $quoteReader
     */
    public function __construct(QuoteReaderInterface $quoteReader)
    {
        $this->quoteReader = $quoteReader;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function findOneByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        $quoteTransfer = $this->quoteReader->getByRestCompanyUserCartsRequest($restCompanyUserCartsRequestTransfer);

        if ($quoteTransfer !== null) {
            return (new RestCompanyUserCartsResponseTransfer())
                ->setIsSuccessful(true)
                ->setQuote($quoteTransfer);
        }

        $quoteErrorTransfer = (new QuoteErrorTransfer())
            ->setMessage(CompanyUserCartsRestApiConstants::ERROR_MESSAGE_QUOTE_NOT_FOUND);

        return (new RestCompanyUserCartsResponseTransfer())
            ->setIsSuccessful(false)
            ->addError($quoteErrorTransfer);
    }
}
