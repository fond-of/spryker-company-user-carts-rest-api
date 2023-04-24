<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator;

use ArrayObject;
use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Exception\QuoteNotCreatedException;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Communication\Plugin\PermissionExtension\WriteCompanyUserCartPermissionPlugin;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface;
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
    protected CompanyUserReaderInterface $companyUserReader;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface
     */
    protected QuoteMapperInterface $quoteMapper;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface
     */
    protected QuoteHandlerInterface $quoteHandler;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface
     */
    protected QuoteFinderInterface $quoteFinder;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface
     */
    protected CompanyUserCartsRestApiToQuoteFacadeInterface $quoteFacade;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface
     */
    protected CompanyUserCartsRestApiToPermissionFacadeInterface $permissionFacade;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface $companyUserReader
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface $quoteMapper
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface $quoteHandler
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface $quoteFinder
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface $quoteFacade
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface $permissionFacade
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        CompanyUserReaderInterface $companyUserReader,
        QuoteMapperInterface $quoteMapper,
        QuoteHandlerInterface $quoteHandler,
        QuoteFinderInterface $quoteFinder,
        CompanyUserCartsRestApiToQuoteFacadeInterface $quoteFacade,
        CompanyUserCartsRestApiToPermissionFacadeInterface $permissionFacade,
        LoggerInterface $logger
    ) {
        $this->companyUserReader = $companyUserReader;
        $this->quoteMapper = $quoteMapper;
        $this->quoteHandler = $quoteHandler;
        $this->quoteFinder = $quoteFinder;
        $this->quoteFacade = $quoteFacade;
        $this->permissionFacade = $permissionFacade;
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

        $canCreate = $this->permissionFacade->can(
            WriteCompanyUserCartPermissionPlugin::KEY,
            $companyUserTransfer->getIdCompanyUser(),
        );

        if (!$canCreate) {
            $quoteErrorTransfer = (new QuoteErrorTransfer())
                ->setMessage(CompanyUserCartsRestApiConstants::ERROR_MESSAGE_PERMISSION_DENIED);

            return (new RestCompanyUserCartsResponseTransfer())->setIsSuccessful(false)
                ->setErrors(new ArrayObject([$quoteErrorTransfer]));
        }

        $quoteTransfer = $this->quoteMapper->fromRestCompanyUserCartsRequest($restCompanyUserCartsRequestTransfer)
            ->setCompanyUser($companyUserTransfer);
        $quoteResponseTransfer = $this->quoteFacade->createQuote($quoteTransfer);
        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if (
            $quoteTransfer === null
            || $quoteTransfer->getIdQuote() === null
            || !$quoteResponseTransfer->getIsSuccessful()
        ) {
            return (new RestCompanyUserCartsResponseTransfer())->setIsSuccessful(false)
                ->setErrors($quoteResponseTransfer->getErrors());
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

        $quoteResponseTransfer = $this->quoteFacade->updateQuote($quoteTransfer);
        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if (
            $quoteTransfer === null
            || $quoteTransfer->getIdQuote() === null
            || !$quoteResponseTransfer->getIsSuccessful()
        ) {
            return (new RestCompanyUserCartsResponseTransfer())->setIsSuccessful(false)
                ->setErrors($quoteResponseTransfer->getErrors());
        }

        return $this->quoteFinder->findByIdQuote($quoteTransfer->getIdQuote());
    }
}
