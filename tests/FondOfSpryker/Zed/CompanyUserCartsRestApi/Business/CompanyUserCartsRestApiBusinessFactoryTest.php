<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdater;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiDependencyProvider;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeBridge;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeBridge;
use Spryker\Zed\Kernel\Container;

class CompanyUserCartsRestApiBusinessFactoryTest extends Unit
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $configMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeBridge|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $persistentCartFacadeMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeBridge|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $quoteFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\CompanyUserCartsRestApiBusinessFactory
     */
    protected $factory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->configMock = $this->getMockBuilder(CompanyUserCartsRestApiConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->persistentCartFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToPersistentCartFacadeBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->quoteFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToQuoteFacadeBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new CompanyUserCartsRestApiBusinessFactory();
        $this->factory->setConfig($this->configMock);
        $this->factory->setContainer($this->containerMock);
    }

    /**
     * @return void
     */
    public function testCreateQuoteUpdater(): void
    {
        $this->containerMock->expects(static::atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('get')
            ->withConsecutive(
                [CompanyUserCartsRestApiDependencyProvider::FACADE_QUOTE],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
            )->willReturnOnConsecutiveCalls(
                $this->quoteFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
            );

        static::assertInstanceOf(
            QuoteUpdater::class,
            $this->factory->createQuoteUpdater(),
        );
    }
}
