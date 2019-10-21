<?php


namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use Codeception\Test\Unit;
use FondOfSpryker\Client\CompanyUserQuote\CompanyUserQuoteClientInterface;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;

class CompanyUserCartsRestApiToCompanyUserQuoteClientBridgeTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientBridge
     */
    protected $companyUserCartsRestApiToCompanyUserQuoteClientBridge;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Client\CompanyUserQuote\CompanyUserQuoteClientInterface
     */
    protected $companyUserQuoteClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteCriteriaFilterTransfer
     */
    protected $quoteCriteriaFilterTransfer;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected $quoteCollectionTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->companyUserQuoteClientMock = $this->getMockBuilder(CompanyUserQuoteClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteCriteriaFilterTransfer = $this->getMockBuilder(QuoteCriteriaFilterTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteCollectionTransferMock = $this->getMockBuilder(QuoteCollectionTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserCartsRestApiToCompanyUserQuoteClientBridge = new CompanyUserCartsRestApiToCompanyUserQuoteClientBridge($this->companyUserQuoteClientMock);
    }

    /**
     * @return void
     */
    public function testGetCompanyUserQuoteCollectionByCriteria(): void
    {
        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->with($this->quoteCriteriaFilterTransfer)
            ->willReturn($this->quoteCollectionTransferMock);

        $this->assertInstanceOf(QuoteCollectionTransfer::class, $this->companyUserCartsRestApiToCompanyUserQuoteClientBridge->getCompanyUserQuoteCollectionByCriteria($this->quoteCriteriaFilterTransfer));
    }
}
