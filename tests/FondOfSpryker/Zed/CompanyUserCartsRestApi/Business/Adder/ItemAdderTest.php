<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class ItemAdderTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartFacadeMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\ItemTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $itemTransferMock;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $customerTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdder
     */
    protected $itemAdder;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->persistentCartFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartFacadeInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerTransferMock = $this->getMockBuilder(CustomerTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemAdder = new ItemAdder($this->persistentCartFacadeMock);
    }

    /**
     * @return void
     */
    public function testAddMultiple(): void
    {
        $self = $this;
        $idQuote = 1;

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getIdQuote')
            ->willReturn($idQuote);

        $this->persistentCartFacadeMock->expects(static::atLeastOnce())
            ->method('add')
            ->with(
                static::callback(
                    static function (PersistentCartChangeTransfer $persistentCartChangeTransfer) use ($self, $idQuote) {
                        return $persistentCartChangeTransfer->getCustomer() === $self->customerTransferMock
                            && $persistentCartChangeTransfer->getIdQuote() === $idQuote
                            && $persistentCartChangeTransfer->getItems()->count() === 1
                            && $persistentCartChangeTransfer->getItems()->offsetGet(0) === $self->itemTransferMock;
                    },
                ),
            )->willReturn($this->quoteResponseTransferMock);

        static::assertEquals(
            $this->quoteResponseTransferMock,
            $this->itemAdder->addMultiple($this->quoteTransferMock, [$this->itemTransferMock]),
        );
    }

    /**
     * @return void
     */
    public function testAddMultipleWithEmptyItems(): void
    {
        $this->quoteTransferMock->expects(static::never())
            ->method('getCustomer');

        $this->quoteTransferMock->expects(static::never())
            ->method('getIdQuote');

        $this->persistentCartFacadeMock->expects(static::never())
            ->method('add');

        $quoteResponseTransfer = $this->itemAdder->addMultiple($this->quoteTransferMock, []);

        static::assertTrue($quoteResponseTransfer->getIsSuccessful());

        static::assertEquals(
            $this->quoteTransferMock,
            $quoteResponseTransfer->getQuoteTransfer(),
        );
    }
}
