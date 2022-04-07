<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Communication\Controller;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\CompanyUserCartsRestApiFacade;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

class GatewayControllerTest extends Unit
{
    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsRequestTransferMock;

    /**
     * @var \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $restCompanyUserCartsResponseTransferMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\CompanyUserCartsRestApiFacade|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $facadeMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Communication\Controller\GatewayController
     */
    protected $gatewayController;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->restCompanyUserCartsRequestTransferMock = $this->getMockBuilder(RestCompanyUserCartsRequestTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restCompanyUserCartsResponseTransferMock = $this->getMockBuilder(RestCompanyUserCartsResponseTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->facadeMock = $this->getMockBuilder(CompanyUserCartsRestApiFacade::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->gatewayController = new class ($this->facadeMock) extends GatewayController {
            /**
             * @var \Spryker\Zed\Kernel\Business\AbstractFacade
             */
            protected $companyUserCartsRestApiFacade;

            /**
             * @param \Spryker\Zed\Kernel\Business\AbstractFacade $facade
             */
            public function __construct(AbstractFacade $facade)
            {
                $this->companyUserCartsRestApiFacade = $facade;
            }

            /**
             * @return \Spryker\Zed\Kernel\Business\AbstractFacade
             */
            protected function getFacade(): AbstractFacade
            {
                return $this->companyUserCartsRestApiFacade;
            }
        };
    }

    /**
     * @return void
     */
    public function testCreateQuoteByRestCompanyUserCartsRequestAction(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('createQuoteByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->gatewayController->createQuoteByRestCompanyUserCartsRequestAction(
                $this->restCompanyUserCartsRequestTransferMock,
            ),
        );
    }

    /**
     * @return void
     */
    public function testUpdateQuoteByRestCompanyUserCartsRequestAction(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('updateQuoteByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->gatewayController->updateQuoteByRestCompanyUserCartsRequestAction(
                $this->restCompanyUserCartsRequestTransferMock,
            ),
        );
    }

    /**
     * @return void
     */
    public function testDeleteQuoteByRestCompanyUserCartsRequestAction(): void
    {
        $this->facadeMock->expects(static::atLeastOnce())
            ->method('deleteQuoteByRestCompanyUserCartsRequest')
            ->with($this->restCompanyUserCartsRequestTransferMock)
            ->willReturn($this->restCompanyUserCartsResponseTransferMock);

        static::assertEquals(
            $this->restCompanyUserCartsResponseTransferMock,
            $this->gatewayController->deleteQuoteByRestCompanyUserCartsRequestAction(
                $this->restCompanyUserCartsRequestTransferMock,
            ),
        );
    }
}
