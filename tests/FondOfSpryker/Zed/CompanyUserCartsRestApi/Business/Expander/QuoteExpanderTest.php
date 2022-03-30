<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
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

        $this->quoteExpander = new QuoteExpander($this->configMock);
    }

    /**
     * @return void
     */
    public function testExpand(): void
    {
        $currentData = [
            'name' => 'foo bar',
            'price_mode' => 'NET_MODE',
        ];

        $newData = [
            'name' => 'foo bar2',
            'price_mode' => 'NET_MODE',
            'foo' => 'bar',
        ];

        $allowedFieldsToPatchInQuote = ['name'];

        $this->restCompanyUserCartsRequestTransferMock->expects(static::atLeastOnce())
            ->method('getCart')
            ->willReturn($this->restCartsRequestAttributesTransferMock);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('toArray')
            ->willReturn($currentData);

        $this->configMock->expects(static::atLeastOnce())
            ->method('getAllowedFieldsToPatchInQuote')
            ->willReturn($allowedFieldsToPatchInQuote);

        $this->restCartsRequestAttributesTransferMock->expects(static::atLeastOnce())
            ->method('modifiedToArray')
            ->willReturn($newData);

        $this->quoteTransferMock->expects(static::atLeastOnce())
            ->method('fromArray')
            ->with(
                [
                    'name' => $newData['name'],
                    'price_mode' => $currentData['price_mode'],
                ],
                true,
            )->willReturn($this->quoteTransferMock);

        static::assertEquals(
            $this->quoteTransferMock,
            $this->quoteExpander->expand($this->quoteTransferMock, $this->restCompanyUserCartsRequestTransferMock),
        );
    }
}
