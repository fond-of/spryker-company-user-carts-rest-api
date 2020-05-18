<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use ArrayObject;
use Codeception\Test\Unit;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\DiscountTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestItemsAttributesTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartsResourceMapperTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapper
     */
    protected $cartsResourceMapper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface
     */
    protected $cartItemsResourceMapperMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig
     */
    protected $configMock;

    /**
     * @var array
     */
    protected $allowedFieldsToUpdate;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\QuoteTransfer
     */
    protected $quoteTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface
     */
    protected $restRequestMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CurrencyTransfer
     */
    protected $currencyTransferMock;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\StoreTransfer
     */
    protected $storeTransferMock;

    /**
     * @var string
     */
    protected $nameStore;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\TotalsTransfer
     */
    protected $totalsTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\DiscountTransfer
     */
    protected $discountTransferMock;

    /**
     * @var array
     */
    protected $discountTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    protected $restResourceInterfaceMock;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var \ArrayObject
     */
    protected $itemTransfers;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\ItemTransfer
     */
    protected $itemTransferMock;

    /**
     * @var string
     */
    protected $groupKey;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestItemsAttributesTransfer
     */
    protected $restItemsAttributesTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\TaxTotalTransfer
     */
    protected $taxTotalTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\RestCartsRequestAttributesTransfer
     */
    protected $restCartsRequestAttributesTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->cartItemsResourceMapperMock = $this->getMockBuilder(CartItemsResourceMapperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceBuilderMock = $this->getMockBuilder(RestResourceBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder(CompanyUserCartsRestApiConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restRequestMock = $this->getMockBuilder(RestRequestInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->currencyTransferMock = $this->getMockBuilder(CurrencyTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeTransferMock = $this->getMockBuilder(StoreTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->totalsTransferMock = $this->getMockBuilder(TotalsTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->discountTransferMock = $this->getMockBuilder(DiscountTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restResourceInterfaceMock = $this->getMockBuilder(RestResourceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restItemsAttributesTransferMock = $this->getMockBuilder(RestItemsAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->taxTotalTransferMock = $this->getMockBuilder(TaxTotalTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartsRequestAttributesTransferMock = $this->getMockBuilder(RestCartsRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->code = 'code';

        $this->nameStore = 'name';

        $this->uuid = 'uuid';

        $this->groupKey = 'group key';

        $this->itemTransfers = new ArrayObject([
           $this->itemTransferMock,
        ]);

        $this->allowedFieldsToUpdate = [
            'in',
        ];

        $this->discountTransfers = new ArrayObject([
            $this->discountTransferMock,
        ]);

        $this->cartsResourceMapper = new CartsResourceMapper(
            $this->cartItemsResourceMapperMock,
            $this->restResourceBuilderMock,
            $this->configMock
        );
    }

    /**
     * @return void
     */
    public function testMapCartsResource(): void
    {
        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCurrency')
            ->willReturn($this->currencyTransferMock);

        $this->currencyTransferMock->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn($this->code);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getStore')
            ->willReturn($this->storeTransferMock);

        $this->storeTransferMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($this->nameStore);

        $this->quoteTransferMock->expects($this->atLeast(3))
            ->method('getTotals')
            ->willReturn($this->totalsTransferMock);

        $this->totalsTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->totalsTransferMock->expects($this->atLeastOnce())
            ->method('getTaxTotal')
            ->willReturn($this->taxTotalTransferMock);

        $this->taxTotalTransferMock->expects($this->atLeastOnce())
            ->method('getAmount')
            ->willReturn(1);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getVoucherDiscounts')
            ->willReturn($this->discountTransfers);

        $this->discountTransferMock->expects($this->atLeast(2))
            ->method('toArray')
            ->willReturn([]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCartRuleDiscounts')
            ->willReturn($this->discountTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getUuid')
            ->willReturn($this->uuid);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($this->groupKey);

        $this->cartItemsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapCartItemAttributes')
            ->with($this->itemTransferMock)
            ->willReturn($this->restItemsAttributesTransferMock);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->uuid);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($this->groupKey);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addLink')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addRelationship')
            ->with($this->restResourceInterfaceMock)
            ->willReturn($this->restResourceInterfaceMock);

        $this->assertInstanceOf(
            RestResourceInterface::class,
            $this->cartsResourceMapper->mapCartsResource($this->quoteTransferMock, $this->restRequestMock)
        );
    }

    /**
     * @return void
     */
    public function testMapCartsResourceWithTotalsEqualsNull(): void
    {
        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCurrency')
            ->willReturn($this->currencyTransferMock);

        $this->currencyTransferMock->expects($this->atLeastOnce())
            ->method('getCode')
            ->willReturn($this->code);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getStore')
            ->willReturn($this->storeTransferMock);

        $this->storeTransferMock->expects($this->atLeastOnce())
            ->method('getName')
            ->willReturn($this->nameStore);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getTotals')
            ->willReturn(null);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getVoucherDiscounts')
            ->willReturn($this->discountTransfers);

        $this->discountTransferMock->expects($this->atLeast(2))
            ->method('toArray')
            ->willReturn([]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getCartRuleDiscounts')
            ->willReturn($this->discountTransfers);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getUuid')
            ->willReturn($this->uuid);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('getItems')
            ->willReturn($this->itemTransfers);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($this->groupKey);

        $this->cartItemsResourceMapperMock->expects($this->atLeastOnce())
            ->method('mapCartItemAttributes')
            ->with($this->itemTransferMock)
            ->willReturn($this->restItemsAttributesTransferMock);

        $this->restResourceBuilderMock->expects($this->atLeastOnce())
            ->method('createRestResource')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($this->uuid);

        $this->itemTransferMock->expects($this->atLeastOnce())
            ->method('getGroupKey')
            ->willReturn($this->groupKey);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addLink')
            ->willReturn($this->restResourceInterfaceMock);

        $this->restResourceInterfaceMock->expects($this->atLeastOnce())
            ->method('addRelationship')
            ->with($this->restResourceInterfaceMock)
            ->willReturn($this->restResourceInterfaceMock);

        $this->assertInstanceOf(
            RestResourceInterface::class,
            $this->cartsResourceMapper->mapCartsResource($this->quoteTransferMock, $this->restRequestMock)
        );
    }

    /**
     * @return void
     */
    public function testMapMinimalRestCartsRequestAttributesTransferToQuoteTransferContinue(): void
    {
        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('modifiedToArray')
            ->willReturn(['Test']);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('fromArray')
            ->willReturn($this->quoteTransferMock);

        $this->assertInstanceOf(
            QuoteTransfer::class,
            $this->cartsResourceMapper->mapMinimalRestCartsRequestAttributesTransferToQuoteTransfer($this->restCartsRequestAttributesTransferMock, $this->quoteTransferMock)
        );
    }

    /**
     * @return void
     */
    public function testMapMinimalRestCartsRequestAttributesTransferToQuoteTransfer(): void
    {
        $this->configMock->expects($this->atLeastOnce())
            ->method('getAllowedFieldsToPatchInQuote')
            ->willReturn($this->allowedFieldsToUpdate);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('modifiedToArray')
            ->willReturn(['in' => true]);

        $this->quoteTransferMock->expects($this->atLeastOnce())
            ->method('fromArray')
            ->willReturn($this->quoteTransferMock);

        $this->assertInstanceOf(
            QuoteTransfer::class,
            $this->cartsResourceMapper->mapMinimalRestCartsRequestAttributesTransferToQuoteTransfer($this->restCartsRequestAttributesTransferMock, $this->quoteTransferMock)
        );
    }

    /**
     * @return void
     */
    public function testMapRestCartsRequestAttributesTransferToQuoteTransfer(): void
    {
        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('getCurrency')
            ->willReturn($this->currencyTransferMock);

        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('getStore')
            ->willReturn($this->storeTransferMock);

        $this->restCartsRequestAttributesTransferMock->expects($this->atLeastOnce())
            ->method('toArray')
            ->willReturn([]);

        $this->assertInstanceOf(
            QuoteTransfer::class,
            $this->cartsResourceMapper->mapRestCartsRequestAttributesTransferToQuoteTransfer($this->restCartsRequestAttributesTransferMock)
        );
    }
}
