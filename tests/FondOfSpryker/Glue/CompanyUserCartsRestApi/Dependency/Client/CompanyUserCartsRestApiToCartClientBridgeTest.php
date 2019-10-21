<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Cart\CartClientInterface;

class CompanyUserCartsRestApiToCartClientBridgeTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\Cart\CartClientInterface
     */
    protected $cartClientInterfaceMock;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientBridge
     */
    protected $companyUserCartsRestApiToCartClientBridge;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\ItemTransfer
     */
    protected $itemTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var array
     */
    protected $itemTransfers;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cartClientInterfaceMock = $this->getMockBuilder(CartClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransfers = [
            $this->itemTransferMock,
        ];

        $this->sku = "SKU";

        $this->companyUserCartsRestApiToCartClientBridge = new CompanyUserCartsRestApiToCartClientBridge($this->cartClientInterfaceMock);
    }

    /**
     * @return void
     */
    public function testAddItems(): void
    {
        $this->cartClientInterfaceMock->expects($this->atLeastOnce())
            ->method('addItems')
            ->with($this->itemTransfers, [])
            ->willReturn($this->quoteTransferMock);

        $this->assertInstanceOf(QuoteTransfer::class, $this->companyUserCartsRestApiToCartClientBridge->addItems($this->itemTransfers));
    }

    /**
     * @return void
     */
    public function testReloadItems(): void
    {
        $this->companyUserCartsRestApiToCartClientBridge->reloadItems();
    }

    /**
     * @return void
     */
    public function testRemoveItems(): void
    {
        $this->cartClientInterfaceMock->expects($this->atLeastOnce())
            ->method('removeItems')
            ->with(new ArrayObject($this->itemTransfers))
            ->willReturn($this->quoteTransferMock);

        $this->assertInstanceOf(QuoteTransfer::class, $this->companyUserCartsRestApiToCartClientBridge->removeItems($this->itemTransfers));
    }

    /**
     * @return void
     */
    public function testFindQuoteItem(): void
    {
        $this->cartClientInterfaceMock->expects($this->atLeastOnce())
            ->method('findQuoteItem')
            ->with($this->quoteTransferMock, $this->sku, null)
            ->willReturn($this->itemTransferMock);

        $this->assertInstanceOf(ItemTransfer::class, $this->companyUserCartsRestApiToCartClientBridge->findQuoteItem($this->quoteTransferMock, $this->sku));
    }

    /**
     * @return void
     */
    public function testChangeItemQuantity(): void
    {
        $this->cartClientInterfaceMock->expects($this->atLeastOnce())
            ->method('changeItemQuantity')
            ->with($this->sku)
            ->willReturn($this->quoteTransferMock);

        $this->assertInstanceOf(QuoteTransfer::class, $this->companyUserCartsRestApiToCartClientBridge->changeItemQuantity($this->sku));
    }
}
