<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

class QuoteDeleter implements QuoteDeleterInterface
{
    /**
     * @var string
     */
    protected const ERROR_MESSAGE_QUOTE_NOT_FOUND = 'quote.validation.error.quote_not_found';

    /**
     * @var string
     */
    protected const ERROR_MESSAGE_QUOTE_NOT_DELETED = 'quote.validation.error.quote_not_deleted';

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface $quoteReader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
    ) {
        $this->quoteReader = $quoteReader;
        $this->persistentCartFacade = $persistentCartFacade;
    }

    /**
     * @inheritDoc
     */
    public function deleteByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        $quoteTransfer = $this->quoteReader->getByRestCompanyUserCartsRequest($restCompanyUserCartsRequestTransfer);

        if ($quoteTransfer === null) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(static::ERROR_MESSAGE_QUOTE_NOT_FOUND);

            return (new RestCompanyUserCartsResponseTransfer())->addError($quoteErrorTransfer)
                ->setIsSuccessful(false);
        }

        $quoteResponseTransfer = $this->persistentCartFacade->deleteQuote($quoteTransfer);

        if ($quoteResponseTransfer->getIsSuccessful() === false) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(static::ERROR_MESSAGE_QUOTE_NOT_DELETED);

            return (new RestCompanyUserCartsResponseTransfer())->addError($quoteErrorTransfer)
                ->setIsSuccessful(false);
        }

        return (new RestCompanyUserCartsResponseTransfer())
            ->setIsSuccessful(true);
    }
}
