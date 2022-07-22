<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater;

use Codeception\Test\Unit;
use Exception;
use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Psr\Log\LoggerInterface;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionHandlerInterface;
use Throwable;

class QuoteUpdaterTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteFinderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteExpanderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteUpdateRequestMapperMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteHandlerMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteReloaderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartFacadeMock;

    /**
     * @var \Psr\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $loggerMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteUpdateRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteUpdateRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsResponseTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdater
     */
    protected $quoteUpdater;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->quoteFinderMock = $this->getMockBuilder(QuoteFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteExpanderMock = $this->getMockBuilder(QuoteExpanderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteUpdateRequestMapperMock = $this->getMockBuilder(QuoteUpdateRequestMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteHandlerMock = $this->getMockBuilder(QuoteHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteUpdateRequestTransferMock = $this->getMockBuilder(QuoteUpdateRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsResponseTransferMock = $this->getMockBuilder(RestCompanyUserCartsResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteUpdater = new QuoteUpdater(
            $this->quoteFinderMock,
            $this->quoteExpanderMock,
            $this->quoteUpdateRequestMapperMock,
            $this->quoteHandlerMock,
            $this->persistentCartFacadeMock,
            $this->loggerMock
        );
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $idQuote = 1;

        $this->quoteFinderMock->expects(static::atLeastOnce())
            ->method('findOneByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::never())
            ->method('setIsSuccessful');

        $this->quoteExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteUpdateRequestMapperMock->expects(static::atLeastOnce())
            ->method('fromQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteUpdateRequestTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('updateQuote')
            ->with($this->quoteUpdateRequestTransferMock)
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
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getIdQuote')
            ->willReturn($idQuote);

        $this->quoteFinderMock->expects(static::atLeastOnce())
            ->method('findByIdQuote')
            ->with($idQuote)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->quoteUpdater->updateByRestCompanyUserCartsRequest($this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithNonExistingQuote(): void
    {
        $this->quoteFinderMock->expects(static::atLeastOnce())
            ->method('findOneByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn(null);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturnOnConsecutiveCalls(false, true);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('setIsSuccessful')
            ->with(true)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->quoteExpanderMock->expects(static::never())
            ->method('expand');

        $this->quoteUpdateRequestMapperMock->expects(static::never())
            ->method('fromQuote');

        $this->persistentCartFacadeMock->expects(static::never())
            ->method('updateQuote');

        $this->quoteHandlerMock->expects(static::never())
            ->method('handle');

        $this->quoteFinderMock->expects(static::never())
            ->method('findByIdQuote');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->quoteUpdater->updateByRestCompanyUserCartsRequest(
                $this->restCompanyUserCartsRequestTransferMock,
            ),
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithError(): void
    {
        $this->quoteFinderMock->expects(static::atLeastOnce())
            ->method('findOneByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::never())
            ->method('setIsSuccessful');

        $this->quoteExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteUpdateRequestMapperMock->expects(static::atLeastOnce())
            ->method('fromQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteUpdateRequestTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('updateQuote')
            ->with($this->quoteUpdateRequestTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn(null);

        $this->quoteResponseTransferMock->expects(static::never())
            ->method('getIsSuccessful');

        $this->quoteHandlerMock->expects(static::never())
            ->method('handle');

        $this->quoteFinderMock->expects(static::never())
            ->method('findByIdQuote');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        $restCompanyUserCartsResponseTransfer = $this->quoteUpdater->updateByRestCompanyUserCartsRequest(
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertCount(1, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertFalse($restCompanyUserCartsResponseTransfer->getIsSuccessful());

        static::assertEquals(
            CompanyUserCartsRestApiConstants::ERROR_MESSAGE_QUOTE_NOT_UPDATED,
            $restCompanyUserCartsResponseTransfer->getErrors()
                ->offsetGet(0)
                ->getMessage(),
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithInvalidHandle(): void
    {
        $this->quoteFinderMock->expects(static::atLeastOnce())
            ->method('findOneByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::never())
            ->method('setIsSuccessful');

        $this->quoteExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteUpdateRequestMapperMock->expects(static::atLeastOnce())
            ->method('fromQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteUpdateRequestTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('updateQuote')
            ->with($this->quoteUpdateRequestTransferMock)
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
            ->method('getQuote')
            ->willReturn(null);

        $this->quoteFinderMock->expects(static::never())
            ->method('findByIdQuote');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('serialize');

        $this->loggerMock->expects(static::never())
            ->method('error');

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->quoteUpdater->updateByRestCompanyUserCartsRequest($this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithException(): void
    {
        $exception = new Exception('Foo bar');

        $this->quoteFinderMock->expects(static::atLeastOnce())
            ->method('findOneByRestCompanyUserCartsRequest')
            ->willThrowException($exception);

        $this->quoteExpanderMock->expects(static::never())
            ->method('expand');

        $this->quoteUpdateRequestMapperMock->expects(static::never())
            ->method('fromQuote');

        $this->persistentCartFacadeMock->expects(static::never())
            ->method('updateQuote');

        $this->quoteHandlerMock->expects(static::never())
            ->method('handle');

        $this->quoteFinderMock->expects(static::never())
            ->method('findByIdQuote');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->willReturn('{...}');

        $this->loggerMock->expects(static::atLeastOnce())
            ->method('error')
            ->with(
                'Quote could not be updated.',
                [
                    'exception' => $exception->getMessage(),
                    'trace' => $exception->getTraceAsString(),
                    'data' => '{...}',
                ],
            );

        try {
            $this->quoteUpdater->updateByRestCompanyUserCartsRequest(
                $this->restCompanyUserCartsRequestTransferMock,
            );

            static::fail();
        } catch (Throwable $throwable) {
            static::assertEquals($exception, $throwable);
        }
    }
}
