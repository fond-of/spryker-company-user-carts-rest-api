<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter;

use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

class QuoteDeleter implements QuoteDeleterInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface
     */
    protected $quoteExpander;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface
     */
    protected $quoteFacade;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface $quoteReader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface $quoteExpander
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface $quoteFacade
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        QuoteExpanderInterface $quoteExpander,
        CompanyUserCartsRestApiToQuoteFacadeInterface $quoteFacade
    ) {
        $this->quoteReader = $quoteReader;
        $this->quoteFacade = $quoteFacade;
        $this->quoteExpander = $quoteExpander;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function deleteByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        $quoteTransfer = $this->quoteReader->getByRestCompanyUserCartsRequest($restCompanyUserCartsRequestTransfer);

        if ($quoteTransfer === null) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(CompanyUserCartsRestApiConstants::ERROR_MESSAGE_QUOTE_NOT_FOUND);

            return (new RestCompanyUserCartsResponseTransfer())->addError($quoteErrorTransfer)
                ->setIsSuccessful(false);
        }

        $quoteTransfer = $this->quoteExpander->expand($quoteTransfer, $restCompanyUserCartsRequestTransfer);
        $quoteResponseTransfer = $this->quoteFacade->deleteQuote($quoteTransfer);

        if ($quoteResponseTransfer->getIsSuccessful() === false) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(CompanyUserCartsRestApiConstants::ERROR_MESSAGE_QUOTE_NOT_DELETED);

            return (new RestCompanyUserCartsResponseTransfer())->addError($quoteErrorTransfer)
                ->setIsSuccessful(false);
        }

        return (new RestCompanyUserCartsResponseTransfer())
            ->setIsSuccessful(true);
    }
}
