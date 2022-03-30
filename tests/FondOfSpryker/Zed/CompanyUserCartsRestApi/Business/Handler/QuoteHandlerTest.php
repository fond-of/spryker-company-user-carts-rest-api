<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\ItemUpdaterInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;

class QuoteHandlerTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemsCategorizerMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemAdderMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\ItemUpdaterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemUpdaterMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemRemoverMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteReloaderMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var array<string, array<\Generated\Shared\Transfer\ItemTransfer|\PHPUnit\Framework\MockObject\MockObject>>
     */
    protected $categorizedItemTransferMocks;

    /**
     * @var \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCartsRequestAttributesTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandler
     */
    protected $quoteHandler;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->itemsCategorizerMock = $this->getMockBuilder(ItemsCategorizerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemAdderMock = $this->getMockBuilder(ItemAdderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemUpdaterMock = $this->getMockBuilder(ItemUpdaterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemRemoverMock = $this->getMockBuilder(ItemRemoverInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteReloaderMock = $this->getMockBuilder(QuoteReloaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->categorizedItemTransferMocks = [
            ItemsCategorizerInterface::CATEGORY_ADDABLE => [
                $this->getMockBuilder(ItemTransfer::class)
                    ->disableOriginalConstructor()
                    ->getMock(),
            ],
            ItemsCategorizerInterface::CATEGORY_UPDATABLE => [
                $this->getMockBuilder(ItemTransfer::class)
                    ->disableOriginalConstructor()
                    ->getMock(),
            ],
            ItemsCategorizerInterface::CATEGORY_REMOVABLE => [
                $this->getMockBuilder(ItemTransfer::class)
                    ->disableOriginalConstructor()
                    ->getMock(),
            ],
        ];

        $this->restCartsRequestAttributesTransferMock = $this->getMockBuilder(RestCartsRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteHandler = new QuoteHandler(
            $this->itemsCategorizerMock,
            $this->itemAdderMock,
            $this->itemUpdaterMock,
            $this->itemRemoverMock,
            $this->quoteReloaderMock,
        );
    }

    /**
     * @return void
     */
    public function testHandle(): void
    {
        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCart')
            ->willReturn($this->restCartsRequestAttributesTransferMock);

        $this->itemsCategorizerMock->expects(static::atLeastOnce())
            ->method('categorize')
            ->with($this->quoteTransferMock, $this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->categorizedItemTransferMocks);

        $this->itemAdderMock->expects(static::atLeastOnce())
            ->method('addMultiple')
            ->with(
                $this->quoteTransferMock,
                $this->categorizedItemTransferMocks[ItemsCategorizerInterface::CATEGORY_ADDABLE],
            )->willReturn([]);

        $this->itemRemoverMock->expects(static::atLeastOnce())
            ->method('removeMultiple')
            ->with(
                $this->quoteTransferMock,
                $this->categorizedItemTransferMocks[ItemsCategorizerInterface::CATEGORY_REMOVABLE],
            )->willReturn([]);

        $this->itemUpdaterMock->expects(static::atLeastOnce())
            ->method('updateMultiple')
            ->with(
                $this->quoteTransferMock,
                $this->categorizedItemTransferMocks[ItemsCategorizerInterface::CATEGORY_UPDATABLE],
            )->willReturn([]);

        $this->quoteReloaderMock->expects(static::atLeastOnce())
            ->method('reload')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteResponseTransferMock->expects(static::atLeastOnce())
            ->method('getErrors')
            ->willReturn(new ArrayObject());

        $restCompanyUserCartsResponseTransfer = $this->quoteHandler->handle(
            $this->quoteTransferMock,
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertTrue($restCompanyUserCartsResponseTransfer->getIsSuccessful());
        static::assertCount(0, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertEquals($this->quoteTransferMock, $restCompanyUserCartsResponseTransfer->getQuote());
    }

    /**
     * @return void
     */
    public function testHandleWithInvalidData(): void
    {
        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCart')
            ->willReturn(null);

        $this->itemsCategorizerMock->expects(static::never())
            ->method('categorize');

        $this->itemAdderMock->expects(static::never())
            ->method('addMultiple');

        $this->itemRemoverMock->expects(static::never())
            ->method('removeMultiple');

        $this->itemUpdaterMock->expects(static::never())
            ->method('updateMultiple');

        $this->quoteReloaderMock->expects(static::never())
            ->method('reload');

        $restCompanyUserCartsResponseTransfer = $this->quoteHandler->handle(
            $this->quoteTransferMock,
            $this->restCompanyUserCartsRequestTransferMock,
        );

        static::assertTrue($restCompanyUserCartsResponseTransfer->getIsSuccessful());
        static::assertCount(0, $restCompanyUserCartsResponseTransfer->getErrors());
        static::assertEquals($this->quoteTransferMock, $restCompanyUserCartsResponseTransfer->getQuote());
    }
}
