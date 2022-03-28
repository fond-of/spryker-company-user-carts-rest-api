<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use Codeception\Test\Unit;
use Exception;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserReferenceClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\QuoteErrorTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestUserTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartCreatorTest extends Unit
{
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
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartCreator
     */
    protected $cartCreator;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCartsRequestAttributesTransfer
     */
    protected $restCartsRequestAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestUserTransfer
     */
    protected $restUserTransferMock;

    /**
     * @var string
     */
    protected $naturalIdentifier;

    /**
     * @var int
     */
    protected $surrogateIdentifier;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    protected $restResourceInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteResponseTransfer
     */
    protected $quoteResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCartItemTransfer
     */
    protected $restCartItemTransferMock;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restResponseInterfaceMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteErrorTransfer
     */
    protected $quoteErrorTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserReferenceClientInterface
     */
    protected $companyUserReferenceClientMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected $restApiErrorMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    protected $companyUserResponseTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CompanyUserTransfer
     */
    protected $companyUserTransferMock;

    /**
     * @var \ArrayObject
     */
    protected $errors;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cartOperationMock = $this->getMockBuilder(CartOperationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartClientMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartClientInterface::class)
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

        $this->restCartsRequestAttributesTransferMock = $this->getMockBuilder(RestCartsRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restUserTransferMock = $this->getMockBuilder(RestUserTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceInterfaceMock = $this->getMockBuilder(RestResourceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteResponseTransferMock = $this->getMockBuilder(QuoteResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartItemTransferMock = $this->getMockBuilder(RestCartItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResponseInterfaceMock = $this->getMockBuilder(RestResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteErrorTransferMock = $this->getMockBuilder(QuoteErrorTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserReferenceClientMock = $this->getMockBuilder(CompanyUserCartsRestApiToCompanyUserReferenceClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restApiErrorMock = $this->getMockBuilder(RestApiErrorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserResponseTransferMock = $this->getMockBuilder(CompanyUserResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserTransferMock = $this->getMockBuilder(CompanyUserTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->naturalIdentifier = 'DE-CU--1';
        $this->surrogateIdentifier = 1;

        $this->errors = new ArrayObject([
            $this->quoteErrorTransferMock,
        ]);

        $this->items = new ArrayObject([
            $this->restCartItemTransferMock,
        ]);

        $this->cartCreator = new CartCreator(
            $this->cartOperationMock,
            $this->persistentCartClientMock,
            $this->cartsResourceMapperMock,
            $this->restResourceBuilderMock,
            $this->companyUserReferenceClientMock,
            $this->restApiErrorMock,
        );
    }

    /**
     * @return void
     */
    public function testCreate(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getRestUser')
            ->willReturn($this->restUserTransferMock);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->naturalIdentifier);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getSurrogateIdentifier')
            ->willReturn($this->surrogateIdentifier);

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->isInstanceOf(CompanyUserTransfer::class))
            ->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->companyUserTransferMock->expects($this->atLeastOnce())
            ->method('getFkCustomer')
            ->willReturn($this->surrogateIdentifier);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->with(CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USERS)
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->naturalIdentifier);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUser')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomerReference')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUserReference')
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getQuoteTransfer')
            ->willReturn($this->quoteTransferMock);

        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->items);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapCartsResource')
            ->with($this->quoteTransferMock, $this->restRequestMock)
            ->willReturn($this->restResourceInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($this->naturalIdentifier);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getUuid')
            ->willReturn($this->naturalIdentifier);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addLink')
            ->with(RestLinkInterface::LINK_SELF)
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addResource')
            ->with($this->restResourceInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartCreator->create($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testCreateFailedCreatingCartError(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getRestUser')
            ->willReturn($this->restUserTransferMock);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->naturalIdentifier);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getSurrogateIdentifier')
            ->willReturn($this->surrogateIdentifier);

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->isInstanceOf(CompanyUserTransfer::class))
            ->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->companyUserTransferMock->expects($this->atLeastOnce())
            ->method('getFkCustomer')
            ->willReturn($this->surrogateIdentifier);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->with(CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USERS)
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->naturalIdentifier);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUser')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomerReference')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUserReference')
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getErrors')
            ->willReturn(new ArrayObject([]));

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        try {
            $this->cartCreator->create($this->restRequestMock, $this->restCartsRequestAttributesTransferMock);
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testCreateFailedCreatingCartErrorItems(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getRestUser')
            ->willReturn($this->restUserTransferMock);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->naturalIdentifier);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getSurrogateIdentifier')
            ->willReturn($this->surrogateIdentifier);

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->isInstanceOf(CompanyUserTransfer::class))
            ->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->companyUserTransferMock->expects($this->atLeastOnce())
            ->method('getFkCustomer')
            ->willReturn($this->surrogateIdentifier);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->with(CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USERS)
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->naturalIdentifier);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUser')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomerReference')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUserReference')
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->quoteResponseTransferMock->expects($this->atLeast(2))
            ->method('getErrors')
            ->willReturn($this->errors);

        $this->quoteErrorTransferMock->expects($this->atLeast(2))
            ->method('getMessage')
            ->willReturn($this->naturalIdentifier);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        try {
            $this->cartCreator->create($this->restRequestMock, $this->restCartsRequestAttributesTransferMock);
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testCreateFailedCreatingCartErrorItemsContinue(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getRestUser')
            ->willReturn($this->restUserTransferMock);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->naturalIdentifier);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getSurrogateIdentifier')
            ->willReturn($this->surrogateIdentifier);

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->isInstanceOf(CompanyUserTransfer::class))
            ->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->companyUserTransferMock->expects($this->atLeastOnce())
            ->method('getFkCustomer')
            ->willReturn($this->surrogateIdentifier);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->with(CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USERS)
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->naturalIdentifier);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUser')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomerReference')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUserReference')
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->quoteResponseTransferMock->expects($this->atLeast(2))
            ->method('getErrors')
            ->willReturn($this->errors);

        $this->quoteErrorTransferMock->expects($this->atLeastOnce())
            ->method('getMessage')
            ->willReturn(CartsRestApiConfig::EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_CART);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        try {
            $this->cartCreator->create($this->restRequestMock, $this->restCartsRequestAttributesTransferMock);
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testCreateRestResponse(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('getRestUser')
            ->willReturn($this->restUserTransferMock);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getNaturalIdentifier')
            ->willReturn($this->naturalIdentifier);

        $this->restUserTransferMock->expects($this->atLeastOnce())
            ->method('getSurrogateIdentifier')
            ->willReturn($this->surrogateIdentifier);

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->isInstanceOf(CompanyUserTransfer::class))
            ->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->companyUserTransferMock->expects($this->atLeastOnce())
            ->method('getFkCustomer')
            ->willReturn($this->surrogateIdentifier);

        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->with(CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USERS)
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->naturalIdentifier);

        $this->cartsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapRestCartsRequestAttributesTransferToQuoteTransfer')
            ->with($this->restCartsRequestAttributesTransferMock)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomer')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUser')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCustomerReference')
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('setCompanyUserReference')
            ->willReturn($this->quoteTransferMock);

        $this->persistentCartClientMock->expects($this->atLeastOnce())
            ->method('createQuote')
            ->with($this->quoteTransferMock)
            ->willReturn($this->quoteResponseTransferMock);

        $this->quoteResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(true);

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
            ->willReturn($this->naturalIdentifier);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getUuid')
            ->willReturn($this->naturalIdentifier);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addLink')
            ->with(RestLinkInterface::LINK_SELF)
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addResource')
            ->with($this->restResourceInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartCreator->create($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }

    /**
     * @return void
     */
    public function testCreateFindNull(): void
    {
        $this->restRequestMock->expects($this->atLeastOnce())
            ->method('findParentResourceByType')
            ->with(CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USERS)
            ->willReturn(null);

        $this->restUserTransferMock->expects($this->never())
            ->method('getNaturalIdentifier');

        $this->companyUserReferenceClientMock->expects($this->atLeastOnce())
            ->method('findCompanyUserByCompanyUserReference')
            ->with($this->isInstanceOf(CompanyUserTransfer::class))
            ->willReturn($this->companyUserResponseTransferMock);

        $this->companyUserResponseTransferMock->expects($this->atLeastOnce())
            ->method('getIsSuccessful')
            ->willReturn(false);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResponse')
            ->willReturn($this->restResponseInterfaceMock);

        $this->restApiErrorMock->expects($this->atLeastOnce())
            ->method('addCompanyUserNotFoundErrorResponse')
            ->with($this->restResponseInterfaceMock)
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->cartCreator->create($this->restRequestMock, $this->restCartsRequestAttributesTransferMock));
    }
}
