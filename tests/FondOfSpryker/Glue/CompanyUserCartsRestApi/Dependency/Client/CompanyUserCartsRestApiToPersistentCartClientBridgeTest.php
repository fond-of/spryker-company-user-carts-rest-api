<?php


namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\QuoteUpdateRequestTransfer;
use Spryker\Client\PersistentCart\PersistentCartClientInterface;

class CompanyUserCartsRestApiToPersistentCartClientBridgeTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Client\PersistentCart\PersistentCartClientInterface
     */
    protected $persistentCartClientMock;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientBridge
     */
    protected $companyUserCartsRestApiToPersistentCartClientBridge;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteUpdateRequestTransfer
     */
    protected $quoteUpdateRequestTransferMock;

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

        $this->persistentCartClientMock = $this->getMockBuilder(PersistentCartClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteUpdateRequestTransferMock = $this->getMockBuilder(QuoteUpdateRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserCartsRestApiToPersistentCartClientBridge = new CompanyUserCartsRestApiToPersistentCartClientBridge($this->persistentCartClientMock);
    }

    /**
     * @return void
     */
    public function testUpdateQuote(): void
    {
        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('updateQuote')
            ->with($this->quoteUpdateRequestTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->assertInstanceOf(QuoteResponseTransfer::class, $this->companyUserCartsRestApiToPersistentCartClientBridge->updateQuote($this->quoteUpdateRequestTransferMock));
    }

    /**
     * @return void
     */
    public function testCreateQuote(): void
    {
        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->assertInstanceOf(QuoteResponseTransfer::class, $this->companyUserCartsRestApiToPersistentCartClientBridge->createQuote($this->quoteTransferMock));
    }

    /**
     * @return void
     */
    public function testDeleteQuote(): void
    {
        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('deleteQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->assertInstanceOf(QuoteResponseTransfer::class, $this->companyUserCartsRestApiToPersistentCartClientBridge->deleteQuote($this->quoteTransferMock));
    }
}
