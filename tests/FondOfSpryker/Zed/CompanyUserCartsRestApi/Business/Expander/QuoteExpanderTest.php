<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;

class QuoteExpanderTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCartsRequestAttributesTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpander
     */
    protected $quoteExpander;

    /**
     * @var \Generated\Shared\Transfer\CompanyUserTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $companyUserTransferMock;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $customerTransferMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configMock = $this->getMockBuilder(CompanyUserCartsRestApiConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteTransferMock = $this->getMockBuilder(QuoteTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCartsRequestAttributesTransferMock = $this->getMockBuilder(RestCartsRequestAttributesTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->companyUserTransferMock = $this->getMockBuilder(CompanyUserTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->customerTransferMock = $this->getMockBuilder(CustomerTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteExpander = new QuoteExpander($this->configMock);
    }

    /**
     * @return void
     */
    public function testExpand(): void
    {
        $newData = [
            'name' => 'foo bar2',
            'price_mode' => 'NET_MODE',
            'foo' => 'bar',
        ];

        $allowedFieldsToPatchInQuote = ['name'];
        $companyUserReference = 'FOO--CU-1';
        $customerReference = 'FOO--C-1';

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn(null);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomerReference')
            ->willReturnOnConsecutiveCalls(null, $customerReference);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCustomerReference')
            ->willReturn($customerReference);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setCustomerReference')
            ->with($customerReference)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setCustomer')
            ->with(
                static::callback(
                    static function (CustomerTransfer $customerTransfer) use ($customerReference) {
                        return $customerTransfer->getCustomerReference() === $customerReference;
                    },
                ),
            )->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn(null);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturnOnConsecutiveCalls(null, $companyUserReference);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($companyUserReference);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setCompanyUserReference')
            ->with($companyUserReference)
            ->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setCompanyUser')
            ->with(
                static::callback(
                    static function (CompanyUserTransfer $companyUserTransfer) use ($companyUserReference) {
                        return $companyUserTransfer->getCompanyUserReference() === $companyUserReference;
                    },
                ),
            )->willReturn($this->quoteTransferMock);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCart')
            ->willReturn($this->restCartsRequestAttributesTransferMock);

        $this->configMock->expects(static::atLeastOnce())
            ->method('getAllowedFieldsToPatchInQuote')
            ->willReturn($allowedFieldsToPatchInQuote);

        $this->restCartsRequestAttributesTransferMock->expects(static::atLeastOnce())
            ->method('modifiedToArray')
            ->willReturn($newData);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($newData['name'])
            ->willReturn($this->quoteTransferMock);

        static::assertEquals(
            $this->quoteTransferMock,
            $this->quoteExpander->expand($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testExpandWithExistingCompanyUser(): void
    {
        $newData = [
            'name' => 'foo bar2',
            'price_mode' => 'NET_MODE',
            'foo' => 'bar',
        ];

        $allowedFieldsToPatchInQuote = ['name'];
        $customerReference = 'FOO--C-1';

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn(null);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomerReference')
            ->willReturn($customerReference);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('getCustomerReference');

        $this->quoteTransferMock->expects(static::never())
            ->method('setCustomerReference');

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setCustomer')
            ->with(
                static::callback(
                    static function (CustomerTransfer $customerTransfer) use ($customerReference) {
                        return $customerTransfer->getCustomerReference() === $customerReference;
                    },
                ),
            )->willReturn($this->quoteTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn($this->companyUserTransferMock);

        $this->quoteTransferMock->expects(static::never())
            ->method('getCompanyUserReference');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('getCompanyUserReference');

        $this->quoteTransferMock->expects(static::never())
            ->method('setCompanyUserReference');

        $this->quoteTransferMock->expects(static::never())
            ->method('setCompanyUser');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCart')
            ->willReturn($this->restCartsRequestAttributesTransferMock);

        $this->configMock->expects(static::atLeastOnce())
            ->method('getAllowedFieldsToPatchInQuote')
            ->willReturn($allowedFieldsToPatchInQuote);

        $this->restCartsRequestAttributesTransferMock->expects(static::atLeastOnce())
            ->method('modifiedToArray')
            ->willReturn($newData);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($newData['name'])
            ->willReturn($this->quoteTransferMock);

        static::assertEquals(
            $this->quoteTransferMock,
            $this->quoteExpander->expand($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock),
        );
    }

    /**
     * @return void
     */
    public function testExpandWithExistingCustomer(): void
    {
        $newData = [
            'name' => 'foo bar2',
            'price_mode' => 'NET_MODE',
            'foo' => 'bar',
        ];

        $allowedFieldsToPatchInQuote = ['name'];
        $companyUserReference = 'FOO--CU-1';

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($this->customerTransferMock);

        $this->quoteTransferMock->expects(static::never())
            ->method('getCustomerReference');

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('getCustomerReference');

        $this->quoteTransferMock->expects(static::never())
            ->method('setCustomerReference');

        $this->quoteTransferMock->expects(static::never())
            ->method('setCustomer');

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCompanyUser')
            ->willReturn(null);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('getCompanyUserReference')
            ->willReturn($companyUserReference);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::never())
            ->method('getCompanyUserReference');

        $this->quoteTransferMock->expects(static::never())
            ->method('setCompanyUserReference');

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setCompanyUser')
            ->with(
                static::callback(
                    static function (CompanyUserTransfer $companyUserTransfer) use ($companyUserReference) {
                        return $companyUserTransfer->getCompanyUserReference() === $companyUserReference;
                    },
                ),
            )->willReturn($this->quoteTransferMock);

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCart')
            ->willReturn($this->restCartsRequestAttributesTransferMock);

        $this->configMock->expects(static::atLeastOnce())
            ->method('getAllowedFieldsToPatchInQuote')
            ->willReturn($allowedFieldsToPatchInQuote);

        $this->restCartsRequestAttributesTransferMock->expects(static::atLeastOnce())
            ->method('modifiedToArray')
            ->willReturn($newData);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('setName')
            ->with($newData['name'])
            ->willReturn($this->quoteTransferMock);

        static::assertEquals(
            $this->quoteTransferMock,
            $this->quoteExpander->expand($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock),
        );
    }
}
