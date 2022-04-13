<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PersistentCartChangeQuantityTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;
use Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface;

class CompanyUserCartsRestApiToPersistentCartFacadeBridgeTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\PersistentCart\Business\PersistentCartFacadeInterface
     */
    protected $facadeMock;

    /**
     * @var \Generated\Shared\Transfer\PersistentCartChangeQuantityTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartChangeQuantityTransferMock;

    /**
     * @var \Generated\Shared\Transfer\PersistentCartChangeTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartChangeTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteUpdateRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteUpdateRequestTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeBridge
     */
    protected $facadeBridge;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->facadeMock = $this->getMockBuilder(PersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartChangeQuantityTransferMock = $this->getMockBuilder(PersistentCartChangeQuantityTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartChangeTransferMock = $this->getMockBuilder(PersistentCartChangeTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteUpdateRequestTransferMock = $this->getMockBuilder(QuoteUpdateRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->facadeBridge = new CompanyUserCartsRestApiToPersistentCartFacadeBridge($this->facadeMock);
    }

    /**
     * @return void
     */
    public function testAdd(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('add')
            ->with($this->persistentCartChangeTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->facadeBridge->add($this->persistentCartChangeTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testChangeItemQuantity(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('changeItemQuantity')
            ->with($this->persistentCartChangeQuantityTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->facadeBridge->changeItemQuantity($this->persistentCartChangeQuantityTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testRemove(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('remove')
            ->with($this->persistentCartChangeTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->facadeBridge->remove($this->persistentCartChangeTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testUpdateQuote(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('updateQuote')
            ->with($this->quoteUpdateRequestTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->facadeBridge->updateQuote($this->quoteUpdateRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testCreateQuote(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->facadeBridge->createQuote($this->quoteTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testDeleteQuote(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('deleteQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->facadeBridge->deleteQuote($this->quoteTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testReloadItems(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('reloadItems')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->facadeBridge->reloadItems($this->quoteTransferMock),
        );
    }
}
