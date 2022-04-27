<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater;

use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Exception\QuoteNotUpdatedException;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapperInterface;
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
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface
     */
    protected $quoteFinder;

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
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface $quoteFinder
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface $quoteExpander
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapperInterface $quoteUpdateRequestMapper
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface $quoteHandler
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        QuoteFinderInterface $quoteFinder,
        QuoteExpanderInterface $quoteExpander,
        QuoteUpdateRequestMapperInterface $quoteUpdateRequestMapper,
        QuoteHandlerInterface $quoteHandler,
        CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
        LoggerInterface $logger
    ) {
        $this->quoteFinder = $quoteFinder;
        $this->quoteExpander = $quoteExpander;
        $this->quoteUpdateRequestMapper = $quoteUpdateRequestMapper;
        $this->quoteHandler = $quoteHandler;
        $this->persistentCartFacade = $persistentCartFacade;
        $this->logger = $logger;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @throws \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Exception\QuoteNotUpdatedException
     * @throws \Throwable
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
        $restCompanyUserCartsResponseTransfer = $this->quoteFinder->findOneByRestCompanyUserCartsRequest(
            $restCompanyUserCartsRequestTransfer,
        );

        $quoteTransfer = $restCompanyUserCartsResponseTransfer->getQuote();

        if ($quoteTransfer === null || !$restCompanyUserCartsResponseTransfer->getIsSuccessful()) {
            return $restCompanyUserCartsResponseTransfer->setIsSuccessful(true);
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

        if (
            $quoteTransfer === null
            || $quoteTransfer->getIdQuote() === null
            || !$restCompanyUserCartsResponseTransfer->getIsSuccessful()
        ) {
            return $restCompanyUserCartsResponseTransfer;
        }

        return $this->quoteFinder->findByIdQuote($quoteTransfer->getIdQuote());
    }
}
