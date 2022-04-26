<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator;

use Codeception\Test\Unit;
use Exception;
use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionHandlerInterface;
use Throwable;

class QuoteCreatorTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $companyUserReaderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteExpanderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteReloaderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteHandlerMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartFacadeMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

    /**
     * @var \Spryker\Zed\Kernel\Persistence\EntityManager\TransactionHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $transactionHandlerMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\CompanyUserTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $companyUserTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsResponseTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator\QuoteCreator
     */
    protected $quoteCreator;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->companyUserReaderMock = $this->getMockBuilder(CompanyUserReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteExpanderMock = $this->getMockBuilder(QuoteExpanderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteHandlerMock = $this->getMockBuilder(QuoteHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteReloaderMock = $this->getMockBuilder(QuoteReloaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->transactionHandlerMock = $this->getMockBuilder(TransactionHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserTransferMock = $this->getMockBuilder(CompanyUserTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsResponseTransferMock = $this->getMockBuilder(RestCompanyUserCartsResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteCreator = new class (
            $this->companyUserReaderMock,
            $this->quoteExpanderMock,
            $this->quoteHandlerMock,
            $this->quoteReloaderMock,
            $this->persistentCartFacadeMock,
            $this->loggerMock,
            $this->transactionHandlerMock
        ) extends QuoteCreator {
            /**
             * @var \Spryker\Zed\Kernel\Persistence\EntityManager\TransactionHandlerInterface
             */
            protected $transactionHandler;

            /**
             * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface $companyUserReader
             * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface $quoteExpander
             * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface $quoteHandler
             * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface $quoteReloader
             * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
             * @param \Psr\Log\LoggerInterface $logger
             * @param \Spryker\Zed\Kernel\Persistence\EntityManager\TransactionHandlerInterface $transactionHandler
             */
            public function __construct(
                CompanyUserReaderInterface $companyUserReader,
                QuoteExpanderInterface $quoteExpander,
                QuoteHandlerInterface $quoteHandler,
                QuoteReloaderInterface $quoteReloader,
                CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade,
                LoggerInterface $logger,
                TransactionHandlerInterface $transactionHandler
            ) {
                parent::__construct(
                    $companyUserReader,
                    $quoteExpander,
                    $quoteHandler,
                    $quoteReloader,
                    $persistentCartFacade,
                    $logger,
                );
                $this->transactionHandler = $transactionHandler;
            }

            /**
             * @return \Spryker\Zed\Kernel\Persistence\EntityManager\TransactionHandlerInterface
             */
            public function getTransactionHandler(): TransactionHandlerInterface
            {
                return $this->transactionHandler;
            }
        };
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->transactionHandlerMock->expects(static::atLeastOnce())
            ->method('handleTransaction')
            ->willReturnCallback(
                static function ($callable) {
                    $callable();
                },
            );

        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->companyUserTransferMock);

        $this->quoteExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with(
                static::callback(
                    static function (QuoteTransfer $quoteTransfer) {
                        return $quoteTransfer->getIdQuote() === null;
                    },
                ),
                $this->restCompanyUserCartsRequestTransferMock,
            )->willReturn($this->quoteTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteHandlerMock->expects(static::atLeastOnce())
            ->method('handle')
            ->with($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->quoteReloaderMock->expects(static::atLeastOnce())
            ->method('reload')
            ->with($this->quoteTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->quoteCreator->createByRestCompanyUserCartsRequest($this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testCreateWithInvalidHandle(): void
    {
        $this->transactionHandlerMock->expects(static::atLeastOnce())
            ->method('handleTransaction')
            ->willReturnCallback(
                static function ($callable) {
                    $callable();
                },
            );

        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->companyUserTransferMock);

        $this->quoteExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with(
                static::callback(
                    static function (QuoteTransfer $quoteTransfer) {
                        return $quoteTransfer->getIdQuote() === null;
                    },
                ),
                $this->restCompanyUserCartsRequestTransferMock,
            )->willReturn($this->quoteTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteHandlerMock->expects(static::atLeastOnce())
            ->method('handle')
            ->with($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn(null);

        $this->quoteReloaderMock->expects(static::never())
            ->method('reload');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->quoteCreator->createByRestCompanyUserCartsRequest($this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testCreateWithNonExistingQuote(): void
    {
        $this->transactionHandlerMock->expects(static::atLeastOnce())
            ->method('handleTransaction')
            ->willReturnCallback(
                static function ($callable) {
                    $callable();
                },
            );

        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn(null);

        $this->quoteExpanderMock->expects(static::never())
            ->method('expand');

        $this->persistentCartFacadeMock->expects(static::never())
            ->method('updateQuote');

        $this->quoteHandlerMock->expects(static::never())
            ->method('handle');

        $this->quoteReloaderMock->expects(static::never())
            ->method('reload');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        $restCompanyUserCartsResponseTransfer = $this->quoteCreator->createByRestCompanyUserCartsRequest(
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertCount(1, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertFalse($restCompanyUserCartsResponseTransfer->getIsSuccessful());

        static::assertEquals(
            CompanyUserCartsRestApiConstants::ERROR_MESSAGE_COMPANY_USER_NOT_FOUND,
            $restCompanyUserCartsResponseTransfer->getErrors()
                ->offsetGet(0)
                ->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testCreateWithError(): void
    {
        $this->transactionHandlerMock->expects(static::atLeastOnce())
            ->method('handleTransaction')
            ->willReturnCallback(
                static function ($callable) {
                    $callable();
                },
            );

        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->companyUserTransferMock);

        $this->quoteExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with(
                static::callback(
                    static function (QuoteTransfer $quoteTransfer) {
                        return $quoteTransfer->getIdQuote() === null;
                    },
                ),
                $this->restCompanyUserCartsRequestTransferMock,
            )
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn(null);

        $this->quoteResponseTransferMock->expects(static::never())
            ->method('getIsSuccessful');

        $this->quoteHandlerMock->expects(static::never())
            ->method('handle');

        $this->quoteReloaderMock->expects(static::never())
            ->method('reload');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        $restCompanyUserCartsResponseTransfer = $this->quoteCreator->createByRestCompanyUserCartsRequest(
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertCount(1, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertFalse($restCompanyUserCartsResponseTransfer->getIsSuccessful());

        static::assertEquals(
            CompanyUserCartsRestApiConstants::ERROR_MESSAGE_QUOTE_NOT_CREATED,
            $restCompanyUserCartsResponseTransfer->getErrors()
                ->offsetGet(0)
                ->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testCreateWithException(): void
    {
        $exception = new Exception('Foo bar');

        $this->transactionHandlerMock->expects(static::atLeastOnce())
            ->method('handleTransaction')
            ->willThrowException($exception);

        $this->companyUserReaderMock->expects(static::never())
            ->method('getByRestCompanyUserCartsRequest');

        $this->quoteExpanderMock->expects(static::never())
            ->method('expand');

        $this->persistentCartFacadeMock->expects(static::never())
            ->method('createQuote');

        $this->quoteHandlerMock->expects(static::never())
            ->method('handle');

        $this->quoteReloaderMock->expects(static::never())
            ->method('reload');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->willReturn('{...}');

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('error')
            ->with(
                'Quote could not be created.',
                [
                    'exception' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                    'data' => '{...}',
                ],
            );

        try {
            $this->quoteCreator->createByRestCompanyUserCartsRequest(
                $this->restCompanyUserCartsRequestTransferMock,
            );
            static::fail();
        } catch (Throwable $throwable) {
            static::assertEquals($exception, $throwable);
        }
    }
}
