<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation;

use Codeception\Test\Unit;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

class RestApiErrorTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiError
     */
    protected $restApiError;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected $restResponseInterfaceMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->restResponseInterfaceMock = $this->getMockBuilder(RestResponseInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->restApiError = new RestApiError();
    }

    /**
     * @return void
     */
    public function testAddCouldNotUpdateCartError(): void
    {
        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->restApiError->addCouldNotUpdateCartError($this->restResponseInterfaceMock));
    }

    /**
     * @return void
     */
    public function testAddCartNotFoundError(): void
    {
        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->restApiError->addCartNotFoundError($this->restResponseInterfaceMock));
    }

    /**
     * @return void
     */
    public function testAddRequiredParameterIsMissingError(): void
    {
        $this->restResponseInterfaceMock->expects($this->atLeastOnce())
            ->method('addError')
            ->willReturn($this->restResponseInterfaceMock);

        $this->assertInstanceOf(RestResponseInterface::class, $this->restApiError->addRequiredParameterIsMissingError($this->restResponseInterfaceMock));
    }
}
