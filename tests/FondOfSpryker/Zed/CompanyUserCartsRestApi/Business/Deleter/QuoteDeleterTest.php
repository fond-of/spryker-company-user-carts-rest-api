<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;

class QuoteDeleterTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteReaderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartFacadeMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter\QuoteDeleter
     */
    protected $quoteDeleter;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->quoteReaderMock = $this->getMockBuilder(QuoteReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteDeleter = new QuoteDeleter(
            $this->quoteReaderMock,
            $this->persistentCartFacadeMock,
        );
    }

    /**
     * @return void
     */
    public function testDeleteByRestCompanyUserCartsRequest(): void
    {
        $this->quoteReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('deleteQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $restCompanyUserCartsResponseTransfer = $this->quoteDeleter->deleteByRestCompanyUserCartsRequest(
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertTrue($restCompanyUserCartsResponseTransfer->getIsSuccessful());
        static::assertCount(0, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertEquals(null, $restCompanyUserCartsResponseTransfer->getQuote());
    }

    /**
     * @return void
     */
    public function testDeleteByRestCompanyUserCartsRequestWithNonExistingQuote(): void
    {
        $this->quoteReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn(null);

        $this->persistentCartFacadeMock->expects(static::never())
            ->method('deleteQuote');

        $restCompanyUserCartsResponseTransfer = $this->quoteDeleter->deleteByRestCompanyUserCartsRequest(
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertFalse($restCompanyUserCartsResponseTransfer->getIsSuccessful());
        static::assertCount(1, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertEquals(
            'quote.validation.error.quote_not_found',
            $restCompanyUserCartsResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
        static::assertEquals(null, $restCompanyUserCartsResponseTransfer->getQuote());
    }

    /**
     * @return void
     */
    public function testDeleteByRestCompanyUserCartsRequestWithError(): void
    {
        $this->quoteReaderMock->expects(static::atLeastOnce())
            ->method('getByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('deleteQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $restCompanyUserCartsResponseTransfer = $this->quoteDeleter->deleteByRestCompanyUserCartsRequest(
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertFalse($restCompanyUserCartsResponseTransfer->getIsSuccessful());
        static::assertCount(1, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertEquals(
            'quote.validation.error.quote_not_deleted',
            $restCompanyUserCartsResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
        static::assertEquals(null, $restCompanyUserCartsResponseTransfer->getQuote());
    }
}
