<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartUpdaterTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartUpdater
     */
    protected $cartUpdater;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface
     */
    protected $cartReaderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    protected $cartOperationMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface
     */
    protected $persistentCartClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected $cartsResourceMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected $restApiErrorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCartsRequestAttributesTransfer
     */
    protected $restCartsRequestAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected $restResponseInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    protected $restResourceInterfaceMock;

    /**
     * @var string
     */
    protected $string;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CustomerTransfer
     */
    protected $customerTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCartItemTransfer
     */
    protected $restCartItemTransferMock;

    /**
     * @var \ArrayObject
     */
    protected $restCartItems;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cartReaderMock = $this->getMockBuilder(CartReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartOperationMock = $this->getMockBuilder(CartOperationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartClientMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartsResourceMapperMock = $this->getMockBuilder(CartsResourceMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restApiErrorMock = $this->getMockBuilder(RestApiErrorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceBuilderMock = $this->getMockBuilder(RestResourceBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartsRequestAttributesTransferMock = $this->getMockBuilder(RestCartsRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restRequestMock = $this->getMockBuilder(RestRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResponseInterfaceMock = $this->getMockBuilder(RestResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceInterfaceMock = $this->getMockBuilder(RestResourceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerTransferMock = $this->getMockBuilder(CustomerTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItemTransferMock = $this->getMockBuilder(RestCartItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItems = new ArrayObject([
           $this->restCartItemTransferMock,
        ]);

        $this->string = "string";

        $this->cartUpdater = new CartUpdater(
            $this->cartReaderMock,
            $this->cartOperationMock,
            $this->persistentCartClientMock,
            $this->cartsResourceMapperMock,
            $this->restApiErrorMock,
            $this->restResourceBuilderMock
        );
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->cartReaderMock->expects($this->atLeastOnce())
            ->method('getQuoteTransferByUuid')
            ->with($this->string, $this->restRequestMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapMinimalRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock, $this->quoteTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getIdQuote')
            ->willReturn(1);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('updateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->restCartItems);

        $this->cartOperationMock->expects($this->atLeastOnce())
            ->method('setQuoteTransfer')
            ->with($this->quoteTransferMock)
            ->willReturn($this->cartOperationMock);

        $this->cartOperationMock->expects($this->atLeastOnce())
            ->method('handleItems')
            ->with($this->restCartItems)
            ->willReturn($this->cartOperationMock);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapCartsResource')
            ->with($this->quoteTransferMock, $this->restRequestMock)
            ->willReturn($this->restResourceInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($this->string);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getUuid')
            ->willReturn($this->string);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addLink')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addResource')
            ->with($this->restResourceInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartUpdater->update($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testUpdateCartNotFoundException(): void
    {
        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $this->restApiErrorMock->expects($this->atLeastOnce())
            ->method('addRequiredParameterIsMissingError')
            ->with($this->restResponseInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartUpdater->update($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testUpdateCartNotFoundError(): void
    {
        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->cartReaderMock->expects($this->atLeastOnce())
            ->method('getQuoteTransferByUuid')
            ->with($this->string, $this->restRequestMock)
            ->willReturn(null);

        $this->restApiErrorMock->expects($this->atLeastOnce())
            ->method('addCartNotFoundError')
            ->with($this->restResponseInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartUpdater->update($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testUpdateCouldNotUpdateCartError(): void
    {
        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->cartReaderMock->expects($this->atLeastOnce())
            ->method('getQuoteTransferByUuid')
            ->with($this->string, $this->restRequestMock)
            ->willReturn($this->quoteTransferMock);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapMinimalRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock, $this->quoteTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getIdQuote')
            ->willReturn(1);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('updateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $this->restApiErrorMock->expects($this->atLeastOnce())
            ->method('addCouldNotUpdateCartError')
            ->with($this->restResponseInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartUpdater->update($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testUpdateCreateRestResponse(): void
    {
        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->cartReaderMock->expects($this->atLeastOnce())
            ->method('getQuoteTransferByUuid')
            ->with($this->string, $this->restRequestMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapMinimalRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock, $this->quoteTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getIdQuote')
            ->willReturn(1);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('updateQuote')
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn(new ArrayObject([]));

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapCartsResource')
            ->with($this->quoteTransferMock, $this->restRequestMock)
            ->willReturn($this->restResourceInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($this->string);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getUuid')
            ->willReturn($this->string);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addLink')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addResource')
            ->with($this->restResourceInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartUpdater->update($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }
}
