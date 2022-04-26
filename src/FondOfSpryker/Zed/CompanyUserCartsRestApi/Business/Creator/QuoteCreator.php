<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator;

use ArrayObject;
use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Exception\QuoteNotCreatedException;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Throwable;

class QuoteCreator implements QuoteCreatorInterface
{
    use TransactionTrait;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface
     */
    protected $companyUserReader;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface
     */
    protected $quoteExpander;

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
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface $companyUserReader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface $quoteExpander
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface $quoteHandler
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface $quoteReloader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        CompanyUserReaderInterface $companyUserReader,
        QuoteExpanderInterface $quoteExpander,
        QuoteHandlerInterface $quoteHandler,
        QuoteReloaderInterface $quoteReloader,
        CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
        LoggerInterface $logger
    ) {
        $this->companyUserReader = $companyUserReader;
        $this->quoteExpander = $quoteExpander;
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
    public function createByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        $self = $this;
        $restCompanyUserCartsResponseTransfer = null;

        try {
            $this->getTransactionHandler()->handleTransaction(
                static function () use ($restCompanyUserCartsRequestTransfer, &$restCompanyUserCartsResponseTransfer, $self): void {
                    $restCompanyUserCartsResponseTransfer = $self->executeCreateByRestCompanyUserCartsRequest(
                        $restCompanyUserCartsRequestTransfer,
                    );

                    if ($restCompanyUserCartsResponseTransfer->getIsSuccessful()) {
                        return;
                    }

                    throw new QuoteNotCreatedException('Quote could not be created.');
                },
            );
        } catch (QuoteNotCreatedException $exception) {
        } catch (Throwable $exception) {
            $this->logger->error('Quote could not be created.', [
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
    protected function executeCreateByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        $companyUserTransfer = $this->companyUserReader->getByRestCompanyUserCartsRequest(
            $restCompanyUserCartsRequestTransfer,
        );

        if ($companyUserTransfer === null) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(CompanyUserCartsRestApiConstants::ERROR_MESSAGE_COMPANY_USER_NOT_FOUND);

            return (new RestCompanyUserCartsResponseTransfer())->setIsSuccessful(false)
                ->setErrors(new ArrayObject([$quoteErrorTransfer]));
        }

        $quoteTransfer = $this->quoteExpander->expand(new QuoteTransfer(), $restCompanyUserCartsRequestTransfer);
        $quoteResponseTransfer = $this->persistentCartFacade->createQuote($quoteTransfer);
        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if ($quoteTransfer === null || !$quoteResponseTransfer->getIsSuccessful()) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(CompanyUserCartsRestApiConstants::ERROR_MESSAGE_QUOTE_NOT_CREATED);

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
