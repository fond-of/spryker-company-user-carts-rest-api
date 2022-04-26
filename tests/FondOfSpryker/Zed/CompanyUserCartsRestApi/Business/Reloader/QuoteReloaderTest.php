<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader;

use Codeception\Test\Unit;
use FondOfSpryker\Shared\CompanyUserCartsRestApi\CompanyUserCartsRestApiConstants;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class QuoteReloaderTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartFacadeMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $customerTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloader
     */
    protected $quoteReloader;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->persistentCartFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerTransferMock = $this->getMockBuilder(CustomerTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteReloader = new QuoteReloader(
            $this->persistentCartFacadeMock,
        );
    }

    /**
     * @return void
     */
    public function testReload(): void
    {
        $idQuote = 1;
        $self = $this;

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getIdQuote')
            ->willReturn($idQuote);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('reloadItems')
            ->with(
                static::callback(
                    static function (QuoteTransfer $quoteTransfer) use ($idQuote, $self) {
                        return $quoteTransfer->getIdQuote() === $idQuote
                            && $quoteTransfer->getCustomer() === $self->customerTransferMock;
                    },
                ),
            )->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $restCompanyUserCartsResponseTransfer = $this->quoteReloader->reload($this->quoteTransferMock);

        static::assertTrue($restCompanyUserCartsResponseTransfer->getIsSuccessful());
        static::assertEquals($this->quoteTransferMock, $restCompanyUserCartsResponseTransfer->getQuote());
        static::assertCount(0, $restCompanyUserCartsResponseTransfer->getErrors());
    }

    /**
     * @return void
     */
    public function testReloadWithError(): void
    {
        $idQuote = 1;
        $self = $this;

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getIdQuote')
            ->willReturn($idQuote);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('reloadItems')
            ->with(
                static::callback(
                    static function (QuoteTransfer $quoteTransfer) use ($idQuote, $self) {
                        return $quoteTransfer->getIdQuote() === $idQuote
                            && $quoteTransfer->getCustomer() === $self->customerTransferMock;
                    },
                ),
            )
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn(null);

        $this->quoteResponseTransferMock->expects(static::never())
            ->method('getIsSuccessful');

        $restCompanyUserCartsResponseTransfer = $this->quoteReloader->reload($this->quoteTransferMock);

        static::assertFalse($restCompanyUserCartsResponseTransfer->getIsSuccessful());
        static::assertEquals(null, $restCompanyUserCartsResponseTransfer->getQuote());
        static::assertCount(1, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertEquals(
            CompanyUserCartsRestApiConstants::ERROR_MESSAGE_ITEMS_NOT_RELOADED,
            $restCompanyUserCartsResponseTransfer->getErrors()->offsetGet(0)->getMessage(),
        );
    }
}
