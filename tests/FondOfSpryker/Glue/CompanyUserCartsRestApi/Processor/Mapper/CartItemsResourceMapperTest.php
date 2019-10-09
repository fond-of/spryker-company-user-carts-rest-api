<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;

class CartItemsResourceMapperTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapper
     */
    protected $cartItemsResourceMapper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCartItemTransfer
     */
    protected $restCartItemTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->restCartItemTransferMock = $this->getMockBuilder(RestCartItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartItemsResourceMapper = new CartItemsResourceMapper();
    }

    /**
     * @return void
     */
    public function testMapRestCartItemTransferToItemTransfer(): void
    {
        $this->restCartItemTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->assertInstanceOf(ItemTransfer::class, $this->cartItemsResourceMapper->mapRestCartItemTransferToItemTransfer($this->restCartItemTransferMock));
    }
}
