<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator;

use Codeception\Test\Unit;
use Exception;
use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Psr\Log\LoggerInterface;
use Throwable;

class QuoteCreatorTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $companyUserReaderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteMapperMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteFinderMock;

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

        $this->quoteMapperMock = $this->getMockBuilder(QuoteMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteHandlerMock = $this->getMockBuilder(QuoteHandlerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteFinderMock = $this->getMockBuilder(QuoteFinderInterface::class)
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

        $this->quoteCreator = new QuoteCreator (
            $this->companyUserReaderMock,
            $this->quoteMapperMock,
            $this->quoteHandlerMock,
            $this->quoteFinderMock,
            $this->persistentCartFacadeMock,
            $this->loggerMock
        );
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $idQuote = 1;

        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->companyUserTransferMock);

        $this->quoteMapperMock->expects(static::atLeastOnce())
            ->method('fromRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->quoteTransferMock);

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
            $this->quoteCreator->createByRestCompanyUserCartsRequest($this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testCreateWithInvalidHandle(): void
    {
        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->companyUserTransferMock);

        $this->quoteMapperMock->expects(static::atLeastOnce())
            ->method('fromRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->quoteTransferMock);

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

        $this->quoteFinderMock->expects(static::never())
            ->method('findByIdQuote');

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
        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn(null);

        $this->quoteMapperMock->expects(static::never())
            ->method('fromRestCompanyUserCartsRequest');

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
        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->companyUserTransferMock);

        $this->quoteMapperMock->expects(static::atLeastOnce())
            ->method('fromRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
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

        $this->quoteFinderMock->expects(static::never())
            ->method('findByIdQuote');

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

        $this->companyUserReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->willThrowException($exception);

        $this->quoteMapperMock->expects(static::never())
            ->method('fromRestCompanyUserCartsRequest');

        $this->persistentCartFacadeMock->expects(static::never())
            ->method('createQuote');

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
