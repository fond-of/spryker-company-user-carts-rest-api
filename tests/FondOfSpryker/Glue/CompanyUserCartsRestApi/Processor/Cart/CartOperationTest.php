<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;

class CartOperationTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperation
     */
    protected $cartOperation;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface
     */
    protected $cartClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface
     */
    protected $quoteClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface
     */
    protected $cartItemsResourceMapperMock;

    /**
     * @var array
     */
    protected $restCartItemExpanderPlugins;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \ArrayObject
     */
    protected $restCartItemTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCartItemTransfer
     */
    protected $restCartItemTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface
     */
    protected $restCartItemExpanderPluginMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\ItemTransfer
     */
    protected $itemTransferMock;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var string
     */
    protected $groupKey;

    /**
     * @var array
     */
    protected $itemTransfersToPersist;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cartClientMock = $this->getMockBuilder(CompanyUserCartsRestApiToCartClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteClientMock = $this->getMockBuilder(CompanyUserCartsRestApiToQuoteClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartItemsResourceMapperMock = $this->getMockBuilder(CartItemsResourceMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItemTransferMock = $this->getMockBuilder(RestCartItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItemExpanderPluginMock = $this->getMockBuilder(RestCartItemExpanderPluginInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sku = "SKU";

        $this->groupKey = "Key";

        $this->itemTransfersToPersist = [
            $this->itemTransferMock,
        ];

        $this->restCartItemExpanderPlugins = [
            $this->restCartItemExpanderPluginMock,
        ];

        $this->restCartItemTransfers = new ArrayObject([
            $this->restCartItemTransferMock,
        ]);

        $this->cartOperation = new CartOperation(
            $this->cartClientMock,
            $this->quoteClientMock,
            $this->cartItemsResourceMapperMock,
            $this->restCartItemExpanderPlugins
        );
    }

    /**
     * @return void
     */
    public function testSetQuoteTransfer(): void
    {
        $this->quoteClientMock->expects($this->atLeastOnce())
            ->method('setQuote')
            ->with($this->quoteTransferMock)
            ->willReturn(true);

        $this->assertInstanceOf(CartOperationInterface::class, $this->cartOperation->setQuoteTransfer($this->quoteTransferMock));
    }

    /**
     * @return void
     */
    public function testHandleItemsRemoveItem(): void
    {
        $this->restCartItemExpanderPluginMock->expects($this->atLeastOnce())
            ->method('expand')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->restCartItemTransferMock);

        $this->cartItemsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartItemTransferToItemTransfer')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->itemTransferMock);

        $this->quoteClientMock->expects($this->atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getSku')
            ->willReturn($this->sku);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($this->groupKey);

        $this->cartClientMock->expects($this->atLeastOnce())
            ->method('findQuoteItem')
            ->with($this->quoteTransferMock)
            ->willReturn($this->itemTransferMock);

        $this->itemTransferMock->expects($this->atLeast(2))
            ->method('getQuantity')
            ->willReturn(0);

        $this->assertInstanceOf(CartOperationInterface::class, $this->cartOperation->handleItems($this->restCartItemTransfers));
    }

    /**
     * @return void
     */
    public function testHandleItemsAddItem(): void
    {
        $this->restCartItemExpanderPluginMock->expects($this->atLeastOnce())
            ->method('expand')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->restCartItemTransferMock);

        $this->cartItemsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartItemTransferToItemTransfer')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->itemTransferMock);

        $this->quoteClientMock->expects($this->atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getSku')
            ->willReturn($this->sku);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($this->groupKey);

        $this->cartClientMock->expects($this->atLeastOnce())
            ->method('findQuoteItem')
            ->with($this->quoteTransferMock)
            ->willReturn(null);

        $this->itemTransferMock->expects($this->atLeast(1))
            ->method('getQuantity')
            ->willReturn(1);

        $this->assertInstanceOf(CartOperationInterface::class, $this->cartOperation->handleItems($this->restCartItemTransfers));
    }

    /**
     * @return void
     */
    public function testHandleItemsUpdateItem(): void
    {
        $this->restCartItemExpanderPluginMock->expects($this->atLeastOnce())
            ->method('expand')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->restCartItemTransferMock);

        $this->cartItemsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartItemTransferToItemTransfer')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->itemTransferMock);

        $this->quoteClientMock->expects($this->atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getSku')
            ->willReturn($this->sku);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($this->groupKey);

        $this->cartClientMock->expects($this->atLeastOnce())
            ->method('findQuoteItem')
            ->with($this->quoteTransferMock)
            ->willReturn($this->itemTransferMock);

        $this->itemTransferMock->expects($this->atLeast(3))
            ->method('getQuantity')
            ->willReturnOnConsecutiveCalls([
                10,
                1,
                10,
            ]);

        $this->assertInstanceOf(CartOperationInterface::class, $this->cartOperation->handleItems($this->restCartItemTransfers));
    }

    /**
     * @return void
     */
    public function testReloadItems(): void
    {
        $this->assertInstanceOf(CartOperationInterface::class, $this->cartOperation->reloadItems());
    }
}
