<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater;

use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Exception\QuoteNotUpdatedException;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Throwable;

class QuoteUpdater implements QuoteUpdaterInterface
{
    use TransactionTrait;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface
     */
    protected $quoteReader;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface
     */
    protected $quoteExpander;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapperInterface
     */
    protected $quoteUpdateRequestMapper;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface
     */
    protected $quoteHandler;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface
     */
    protected $quoteReloader;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface $quoteReader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface $quoteExpander
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapperInterface $quoteUpdateRequestMapper
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface $quoteHandler
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface $quoteReloader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        QuoteReaderInterface $quoteReader,
        QuoteExpanderInterface $quoteExpander,
        QuoteUpdateRequestMapperInterface $quoteUpdateRequestMapper,
        QuoteHandlerInterface $quoteHandler,
        QuoteReloaderInterface $quoteReloader,
        CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
        LoggerInterface $logger
    ) {
        $this->quoteReader = $quoteReader;
        $this->quoteExpander = $quoteExpander;
        $this->quoteUpdateRequestMapper = $quoteUpdateRequestMapper;
        $this->quoteHandler = $quoteHandler;
        $this->quoteReloader = $quoteReloader;
        $this->persistentCartFacade = $persistentCartFacade;
        $this->logger = $logger;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function updateByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        $self = $this;
        $restCompanyUserCartsResponseTransfer = null;

        try {
            $this->getTransactionHandler()->handleTransaction(
                static function () use ($restCompanyUserCartsRequestTransfer, &$restCompanyUserCartsResponseTransfer, $self): void {
                    $restCompanyUserCartsResponseTransfer = $self->executeUpdateByRestCompanyUserCartsRequest(
                        $restCompanyUserCartsRequestTransfer,
                    );

                    if ($restCompanyUserCartsResponseTransfer->getIsSuccessful()) {
                        return;
                    }

                    throw new QuoteNotUpdatedException('Quote could not be updated.');
                },
            );
        } catch (QuoteNotUpdatedException $exception) {
        } catch (Throwable $exception) {
            $this->logger->error('Quote could not be updated.', [
                'exception' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'data' => $restCompanyUserCartsRequestTransfer->serialize()]);

            throw $exception;
        }

        return $restCompanyUserCartsResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    protected function executeUpdateByRestCompanyUserCartsRequest(
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
        $quoteUpdateRequestTransfer = $this->quoteUpdateRequestMapper->fromQuote($quoteTransfer);
        $quoteResponseTransfer = $this->persistentCartFacade->updateQuote($quoteUpdateRequestTransfer);
        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if ($quoteTransfer === null || !$quoteResponseTransfer->getIsSuccessful()) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(CompanyUserCartsRestApiConstants::ERROR_MESSAGE_QUOTE_NOT_UPDATED);

            return (new RestCompanyUserCartsResponseTransfer())->addError($quoteErrorTransfer)
                ->setIsSuccessful(false);
        }

        $restCompanyUserCartsResponseTransfer = $this->quoteHandler->handle(
            $quoteTransfer,
            $restCompanyUserCartsRequestTransfer,
        );

        $quoteTransfer = $restCompanyUserCartsResponseTransfer->getQuote();

        if ($quoteTransfer === null || !$restCompanyUserCartsResponseTransfer->getIsSuccessful()) {
            return $restCompanyUserCartsResponseTransfer;
        }

        return $this->quoteReloader->reload($quoteTransfer);
    }
}
