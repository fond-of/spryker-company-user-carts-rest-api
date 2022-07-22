<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapperInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;

class ItemsCategorizerTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemMapperMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemFinderMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCartRequestAttributesTransferMock;

    /**
     * @var array<\Generated\Shared\Transfer\ItemTransfer|\PHPUnit\Framework\MockObject\MockObject>
     */
    protected $itemTransferMocks;

    /**
     * @var array<\Generated\Shared\Transfer\ItemTransfer|\PHPUnit\Framework\MockObject\MockObject>
     */
    protected $newItemTransferMocks;

    /**
     * @var array<\Generated\Shared\Transfer\RestCartItemTransfer|\PHPUnit\Framework\MockObject\MockObject>
     */
    protected $restCartItemTransferMocks;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizer
     */
    protected $itemsCategorizer;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->itemMapperMock = $this->getMockBuilder(ItemMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemFinderMock = $this->getMockBuilder(ItemFinderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartRequestAttributesTransferMock = $this->getMockBuilder(RestCartsRequestAttributesTransfer::class)
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

        $this->newItemTransferMocks = [
            $this->getMockBuilder(ItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
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

        $this->restCartItemTransferMocks = [
            $this->getMockBuilder(RestCartItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(RestCartItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(RestCartItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
            $this->getMockBuilder(RestCartItemTransfer::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->itemsCategorizer = new ItemsCategorizer(
            $this->itemMapperMock,
            $this->itemFinderMock,
        );
    }

    /**
     * @return void
     */
    public function testCategorize(): void
    {
        $newQuantities = [2, 0, 2, 1];
        $currentQuantities = [1, 2];

        $this->restCartRequestAttributesTransferMock->expects(static::atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayObject($this->restCartItemTransferMocks));

        $this->itemFinderMock->expects(static::atLeastOnce())
            ->method('findInQuoteByRestCartItem')
            ->withConsecutive(
                [$this->quoteTransferMock, $this->restCartItemTransferMocks[0]],
                [$this->quoteTransferMock, $this->restCartItemTransferMocks[1]],
                [$this->quoteTransferMock, $this->restCartItemTransferMocks[2]],
                [$this->quoteTransferMock, $this->restCartItemTransferMocks[3]],
            )->willReturnOnConsecutiveCalls(
                null,
                $this->itemTransferMocks[0],
                $this->itemTransferMocks[1],
                $this->itemTransferMocks[2],
            );

        $this->itemMapperMock->expects(static::atLeastOnce())
            ->method('fromRestCartItem')
            ->withConsecutive(
                [$this->restCartItemTransferMocks[0]],
                [$this->restCartItemTransferMocks[1]],
                [$this->restCartItemTransferMocks[2]],
                [$this->restCartItemTransferMocks[3]],
            )->willReturnOnConsecutiveCalls(
                $this->newItemTransferMocks[0],
                $this->newItemTransferMocks[1],
                $this->newItemTransferMocks[2],
                $this->newItemTransferMocks[3],
            );

        $this->restCartItemTransferMocks[0]->expects(static::atLeastOnce())
            ->method('getQuantity')
            ->willReturn($newQuantities[0]);

        $this->restCartItemTransferMocks[1]->expects(static::atLeastOnce())
            ->method('getQuantity')
            ->willReturn($newQuantities[1]);

        $this->restCartItemTransferMocks[2]->expects(static::atLeastOnce())
            ->method('getQuantity')
            ->willReturn($newQuantities[2]);

        $this->itemTransferMocks[1]->expects(static::atLeastOnce())
            ->method('getQuantity')
            ->willReturn($currentQuantities[0]);

        $this->newItemTransferMocks[2]->expects(static::atLeastOnce())
            ->method('setQuantity')
            ->with($newQuantities[2] - $currentQuantities[0])
            ->willReturn($this->newItemTransferMocks[2]);

        $this->restCartItemTransferMocks[3]->expects(static::atLeastOnce())
            ->method('getQuantity')
            ->willReturn($newQuantities[3]);

        $this->itemTransferMocks[2]->expects(static::atLeastOnce())
            ->method('getQuantity')
            ->willReturn($currentQuantities[1]);

        $this->newItemTransferMocks[3]->expects(static::atLeastOnce())
            ->method('setQuantity')
            ->with(abs($newQuantities[3] - $currentQuantities[1]))
            ->willReturn($this->newItemTransferMocks[3]);

        $categorisedItemTransfers = $this->itemsCategorizer->categorize(
            $this->quoteTransferMock,
            $this->restCartRequestAttributesTransferMock,
        );

        static::assertCount(2, $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_ADDABLE]);
        static::assertCount(2, $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_REMOVABLE]);

        static::assertEquals(
            $this->newItemTransferMocks[0],
            $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_ADDABLE][0],
        );

        static::assertEquals(
            $this->newItemTransferMocks[1],
            $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_REMOVABLE][0],
        );

        static::assertEquals(
            $this->newItemTransferMocks[2],
            $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_ADDABLE][1],
        );

        static::assertEquals(
            $this->newItemTransferMocks[3],
            $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_REMOVABLE][1],
        );
    }
}
