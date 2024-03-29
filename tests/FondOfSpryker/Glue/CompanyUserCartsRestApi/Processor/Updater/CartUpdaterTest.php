<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Updater;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Client\CompanyUserCartsRestApi\CompanyUserCartsRestApiClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpanderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapperInterface;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartUpdaterTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapperInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestMapperMock;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpanderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCartItemExpanderMock;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restResponseBuilderMock;

    /**
     * @var \FondOfSpryker\Client\CompanyUserCartsRestApi\CompanyUserCartsRestApiClientInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $companyUserCartsRestApiClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restRequestMock;

    /**
     * @var \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCartsRequestAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected $restResponseMock;

    /**
     * @var \Generated\Shared\Transfer\RestCartItemTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCartItemTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsResponseTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteErrorTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteErrorTransferMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Updater\CartUpdater
     */
    protected $cartUpdater;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->restCompanyUserCartsRequestMapperMock = $this->getMockBuilder(RestCompanyUserCartsRequestMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItemExpanderMock = $this->getMockBuilder(RestCartItemExpanderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResponseBuilderMock = $this->getMockBuilder(RestResponseBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserCartsRestApiClientMock = $this->getMockBuilder(CompanyUserCartsRestApiClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restRequestMock = $this->getMockBuilder(RestRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartsRequestAttributesTransferMock = $this->getMockBuilder(RestCartsRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResponseMock = $this->getMockBuilder(RestResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItemTransferMock = $this->getMockBuilder(RestCartItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsResponseTransferMock = $this->getMockBuilder(RestCompanyUserCartsResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteErrorTransferMock = $this->getMockBuilder(QuoteErrorTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartUpdater = new CartUpdater(
            $this->restCompanyUserCartsRequestMapperMock,
            $this->restCartItemExpanderMock,
            $this->restResponseBuilderMock,
            $this->companyUserCartsRestApiClientMock,
        );
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $this->restCartsRequestAttributesTransferMock->expects(static::atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayObject([$this->restCartItemTransferMock]));

        $this->restCartItemExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->restCartItemTransferMock);

        $this->restCompanyUserCartsRequestMapperMock->expects(static::atLeastOnce())
            ->method('fromRestRequest')
            ->with($this->restRequestMock)
            ->willReturn($this->restCompanyUserCartsRequestTransferMock);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('setCart')
            ->with($this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->restCompanyUserCartsRequestTransferMock);

        $this->companyUserCartsRestApiClientMock->expects(static::atLeastOnce())
            ->method('updateQuoteByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn($this->quoteTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::never())
            ->method('getErrors');

        $this->restResponseBuilderMock->expects(static::never())
            ->method('buildErrorRestResponse');

        $this->restResponseBuilderMock->expects(static::atLeastOnce())
            ->method('buildRestResponse')
            ->with($this->quoteTransferMock)
            ->willReturn($this->restResponseMock);

        static::assertEquals(
            $this->restResponseMock,
            $this->cartUpdater->update(
                $this->restRequestMock,
                $this->restCartsRequestAttributesTransferMock,
            ),
        );
    }

    /**
     * @return void
     */
    public function testUpdateWithError(): void
    {
        $this->restCartsRequestAttributesTransferMock->expects(static::atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayObject([$this->restCartItemTransferMock]));

        $this->restCartItemExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with($this->restCartItemTransferMock)
            ->willReturn($this->restCartItemTransferMock);

        $this->restCompanyUserCartsRequestMapperMock->expects(static::atLeastOnce())
            ->method('fromRestRequest')
            ->with($this->restRequestMock)
            ->willReturn($this->restCompanyUserCartsRequestTransferMock);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('setCart')
            ->with($this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->restCompanyUserCartsRequestTransferMock);

        $this->companyUserCartsRestApiClientMock->expects(static::atLeastOnce())
            ->method('updateQuoteByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::never())
            ->method('getIsSuccessful');

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getQuote')
            ->willReturn(null);

        $this->restCompanyUserCartsResponseTransferMock->expects(static::atLeastOnce())
            ->method('getErrors')
            ->willReturn(new ArrayObject([$this->quoteErrorTransferMock]));

        $this->restResponseBuilderMock->expects(static::atLeastOnce())
            ->method('buildErrorRestResponse')
            ->with([$this->quoteErrorTransferMock])
            ->willReturn($this->restResponseMock);

        $this->restResponseBuilderMock->expects(static::never())
            ->method('buildRestResponse');

        static::assertEquals(
            $this->restResponseMock,
            $this->cartUpdater->update(
                $this->restRequestMock,
                $this->restCartsRequestAttributesTransferMock,
            ),
        );
    }
}
