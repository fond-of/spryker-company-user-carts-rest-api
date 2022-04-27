<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator;

use ArrayObject;
use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Exception\QuoteNotCreatedException;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\QuoteErrorTransfer;
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
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface
     */
    protected $quoteMapper;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface
     */
    protected $quoteHandler;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface
     */
    protected $quoteFinder;

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
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface $quoteMapper
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface $quoteHandler
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface $quoteFinder
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        CompanyUserReaderInterface $companyUserReader,
        QuoteMapperInterface $quoteMapper,
        QuoteHandlerInterface $quoteHandler,
        QuoteFinderInterface $quoteFinder,
        CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
        LoggerInterface $logger
    ) {
        $this->companyUserReader = $companyUserReader;
        $this->quoteMapper = $quoteMapper;
        $this->quoteHandler = $quoteHandler;
        $this->quoteFinder = $quoteFinder;
        $this->persistentCartFacade = $persistentCartFacade;
        $this->logger = $logger;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @throws \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Exception\QuoteNotCreatedException
     * @throws \Throwable
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

        $quoteTransfer = $this->quoteMapper->fromRestCompanyUserCartsRequest($restCompanyUserCartsRequestTransfer);
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
