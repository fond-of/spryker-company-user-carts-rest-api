<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class QuoteUpdateRequestMapperTest extends Unit
{
    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $customerTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteUpdateRequestMapper
     */
    protected $quoteUpdateRequestMapper;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerTransferMock = $this->getMockBuilder(CustomerTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteUpdateRequestMapper = new QuoteUpdateRequestMapper();
    }

    /**
     * @return void
     */
    public function testFromQuote(): void
    {
        $idQuote = 1;
        $modifiedQuoteData = [
            'name' => 'foo',
        ];

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('modifiedToArray')
            ->willReturn($modifiedQuoteData);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getIdQuote')
            ->willReturn($idQuote);

        $quoteUpdateRequestTransfer = $this->quoteUpdateRequestMapper->fromQuote($this->quoteTransferMock);

        static::assertEquals($idQuote, $quoteUpdateRequestTransfer->getIdQuote());
        static::assertEquals($this->customerTransferMock, $quoteUpdateRequestTransfer->getCustomer());
    }
}
