<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator\QuoteCreatorInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdaterInterface;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

class CompanyUserCartsRestApiFacadeTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\CompanyUserCartsRestApiBusinessFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $factoryMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator\QuoteCreatorInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteCreatorMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdaterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteUpdaterMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsResponseTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\CompanyUserCartsRestApiFacade
     */
    protected $facade;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->factoryMock = $this->getMockBuilder(CompanyUserCartsRestApiBusinessFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteCreatorMock = $this->getMockBuilder(QuoteCreatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteUpdaterMock = $this->getMockBuilder(QuoteUpdaterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsResponseTransferMock = $this->getMockBuilder(RestCompanyUserCartsResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->facade = new CompanyUserCartsRestApiFacade();
        $this->facade->setFactory($this->factoryMock);
    }

    /**
     * @return void
     */
    public function testUpdateQuoteByRestCompanyUserCartsRequest(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createQuoteUpdater')
            ->willReturn($this->quoteUpdaterMock);

        $this->quoteUpdaterMock->expects(static::atLeastOnce())
            ->method('updateByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->facade->updateQuoteByRestCompanyUserCartsRequest($this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testCreateQuoteByRestCompanyUserCartsRequest(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createQuoteCreator')
            ->willReturn($this->quoteCreatorMock);

        $this->quoteCreatorMock->expects(static::atLeastOnce())
            ->method('createByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->facade->createQuoteByRestCompanyUserCartsRequest($this->restCompanyUserCartsRequestTransferMock),
        );
    }
}
