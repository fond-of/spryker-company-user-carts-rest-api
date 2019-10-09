<?php


namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Quote\QuoteClientInterface;

class CompanyUserCartsRestApiToQuoteClientBridgeTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\Quote\QuoteClientInterface
     */
    protected $quoteClientInterfaceMock;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientBridge
     */
    protected $companyUserCartsRestApiToQuoteClientBridge;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->quoteClientInterfaceMock = $this->getMockBuilder(QuoteClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserCartsRestApiToQuoteClientBridge = new CompanyUserCartsRestApiToQuoteClientBridge($this->quoteClientInterfaceMock);
    }

    /**
     * @return void
     */
    public function testSetQuote(): void
    {
        $this->companyUserCartsRestApiToQuoteClientBridge->setQuote($this->quoteTransferMock);
    }

    /**
     * @return void
     */
    /*
    public function testGetQuote(): void
    {
        $this->quoteClientInterfaceMock->expects($this->atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->assertInstanceOf($this->quoteTransferMock, $this->companyUserCartsRestApiToQuoteClientBridge->getQuote());
    }
    */
}
