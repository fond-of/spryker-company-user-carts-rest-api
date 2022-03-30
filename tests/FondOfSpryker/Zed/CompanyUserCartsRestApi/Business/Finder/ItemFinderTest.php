<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;

class ItemFinderTest extends Unit
{
    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCartItemTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCartItemTransferMock;

    /**
     * @var array<\Generated\Shared\Transfer\ItemTransfer|\PHPUnit\Framework\MockObject\MockObject>
     */
    protected $itemTransferMocks;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinder
     */
    protected $itemFinder;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItemTransferMock = $this->getMockBuilder(RestCartItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMocks = [
            $this->getMockBuilder(ItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(ItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->itemFinder = new ItemFinder();
    }

    /**
     * @return void
     */
    public function testFindInQuoteByRestCartItem(): void
    {
        $groupKeys = [
            'foo.bar-1',
            'foo.bar-2',
            'bar.bar-2',
        ];

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->willReturn('{...}');

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayObject($this->itemTransferMocks));

        foreach ($this->itemTransferMocks as $index => $itemTransferMock) {
            $itemTransferMock->expects(static::atLeastOnce())
                ->method('getGroupKey')
                ->willReturn($groupKeys[$index]);
        }

        $this->restCartItemTransferMock->expects(static::atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($groupKeys[1]);

        static::assertEquals(
            $this->itemTransferMocks[1],
            $this->itemFinder->findInQuoteByRestCartItem(
                $this->quoteTransferMock,
                $this->restCartItemTransferMock,
            ),
        );
    }

    /**
     * @return void
     */
    public function testFindInQuoteByRestCartItemWithoutResult(): void
    {
        $groupKeys = [
            'foo.bar-1',
            'foo.bar-2',
            'bar.bar-2',
        ];

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('serialize')
            ->willReturn('{...}');

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayObject($this->itemTransferMocks));

        foreach ($this->itemTransferMocks as $index => $itemTransferMock) {
            $itemTransferMock->expects(static::atLeastOnce())
                ->method('getGroupKey')
                ->willReturn($groupKeys[$index]);
        }

        $this->restCartItemTransferMock->expects(static::atLeastOnce())
            ->method('getGroupKey')
            ->willReturn('foo.bar-3');

        static::assertEquals(
            null,
            $this->itemFinder->findInQuoteByRestCartItem(
                $this->quoteTransferMock,
                $this->restCartItemTransferMock,
            ),
        );
    }
}
