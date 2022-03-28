<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\UserInterface;
use Spryker\Zed\Customer\Business\Customer\CustomerInterface;

class CartReaderTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReader
     */
    protected $cartReader;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    protected $cartOperationMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface
     */
    protected $companyUserQuoteClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected $cartsResourceMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\UserInterface
     */
    protected $userInterfaceMock;

    /**
     * @var string
     */
    protected $string;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    protected $restResourceInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected $quoteCollectionTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected $restResponseInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \ArrayObject
     */
    protected $quoteTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Customer\Business\Customer\CustomerInterface
     */
    protected $customerInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CompanyUserTransfer
     */
    private $companyUserTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected $restApiErrorMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->restApiErrorMock = $this->getMockBuilder(RestApiErrorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartOperationMock = $this->getMockBuilder(CartOperationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserQuoteClientMock = $this->getMockBuilder(CompanyUserCartsRestApiToCompanyUserQuoteClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cartsResourceMapperMock = $this->getMockBuilder(CartsResourceMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceBuilderMock = $this->getMockBuilder(RestResourceBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restRequestMock = $this->getMockBuilder(RestRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userInterfaceMock = $this->getMockBuilder(UserInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceInterfaceMock = $this->getMockBuilder(RestResourceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteCollectionTransferMock = $this->getMockBuilder(QuoteCollectionTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResponseInterfaceMock = $this->getMockBuilder(RestResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerInterfaceMock = $this->getMockBuilder(CustomerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserTransferMock = $this->getMockBuilder(CompanyUserTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->string = 'string';

        $this->quoteTransfers = new ArrayObject([
            $this->quoteTransferMock,
        ]);

        $this->cartReader = new CartReader(
            $this->cartOperationMock,
            $this->companyUserQuoteClientMock,
            $this->cartsResourceMapperMock,
            $this->restResourceBuilderMock,
            $this->restApiErrorMock,
        );
    }

    /**
     * @return void
     */
    public function testReadCompanyUserCartByUuid(): void
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

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($this->string);

        $this->cartOperationMock->expects($this->atLeastOnce())
            ->method('setQuoteTransfer')
            ->with($this->quoteTransferMock)
            ->willReturn($this->cartOperationMock);

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

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartReader->readCompanyUserCartByUuid($this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testReadCompanyUserCartByUuidCardNotFoundException(): void
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

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn(new ArrayObject([]));

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartReader->readCompanyUserCartByUuid($this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testReadCurrentCompanyUserCarts(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeast(3))
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($this->string);

        $this->cartOperationMock->expects($this->atLeastOnce())
            ->method('setQuoteTransfer')
            ->with($this->quoteTransferMock)
            ->willReturn($this->cartOperationMock);

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

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartReader->readCurrentCompanyUserCarts($this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testReadCurrentCompanyUserCartsNoQuotes(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeast(2))
            ->method('getQuotes')
            ->willReturn([]);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartReader->readCurrentCompanyUserCarts($this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testReadCurrentCompanyUserCartsFindNoCompanyUser(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn(null);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeast(3))
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($this->string);

        $this->cartOperationMock->expects($this->atLeastOnce())
            ->method('setQuoteTransfer')
            ->with($this->quoteTransferMock)
            ->willReturn($this->cartOperationMock);

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

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartReader->readCurrentCompanyUserCarts($this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testGetQuoteTransferByUuid(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeast(2))
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->assertInstanceOf(QuoteTransfer::class, $this->cartReader->getQuoteTransferByUuid($this->string, $this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testGetQuoteTransferByUuidNoQuotes(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeastOnce())
            ->method('getQuotes')
            ->willReturn(new ArrayObject([]));

        $this->assertNull($this->cartReader->getQuoteTransferByUuid($this->string, $this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testGetQuoteTransferByUuidFails(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeast(2))
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->assertInstanceOf(QuoteTransfer::class, $this->cartReader->getQuoteTransferByUuid($this->string, $this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testGetQuoteTransferByUuidCustomerNullReferenceNotNull(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeast(2))
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn(null);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomerReference')
            ->willReturn($this->string);

        $this->assertInstanceOf(QuoteTransfer::class, $this->cartReader->getQuoteTransferByUuid($this->string, $this->restRequestMock));
    }

    /**
     * @return void
     */
    public function testGetQuoteTransferByUuidCompanyUserNullCompanyUserReferenceNotNull(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getUser')
            ->willReturn($this->userInterfaceMock);

        $this->userInterfaceMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->string);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->string);

        $this->companyUserQuoteClientMock->expects($this->atLeastOnce())
            ->method('getCompanyUserQuoteCollectionByCriteria')
            ->willReturn($this->quoteCollectionTransferMock);

        $this->quoteCollectionTransferMock->expects($this->atLeast(2))
            ->method('getQuotes')
            ->willReturn($this->quoteTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn(null);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($this->string);

        $this->assertInstanceOf(QuoteTransfer::class, $this->cartReader->getQuoteTransferByUuid($this->string, $this->restRequestMock));
    }
}
