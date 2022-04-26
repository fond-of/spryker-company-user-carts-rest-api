<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business;

use Codeception\Test\Unit;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator\QuoteCreator;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter\QuoteDeleter;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinder;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdater;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiDependencyProvider;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCompanyUserReferenceFacadeBridge;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeBridge;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeBridge;
use Psr\Log\LoggerInterface;
use Spryker\Shared\Log\Config\LoggerConfigInterface;
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
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCompanyUserReferenceFacadeBridge|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $companyUserReferenceFacadeMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Psr\Log\LoggerInterface
     */
    protected $loggerMock;

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

        $this->companyUserReferenceFacadeMock = $this->getMockBuilder(CompanyUserCartsRestApiToCompanyUserReferenceFacadeBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new class ($this->loggerMock) extends CompanyUserCartsRestApiBusinessFactory {
            /**
             * @var \Psr\Log\LoggerInterface
             */
            protected $logger;

            /**
             * @param \Psr\Log\LoggerInterface $logger
             */
            public function __construct(LoggerInterface $logger)
            {
                $this->logger = $logger;
            }

            /**
             * @param \Spryker\Shared\Log\Config\LoggerConfigInterface|null $loggerConfig
             *
             * @return \Psr\Log\LoggerInterface
             */
            protected function getLogger(?LoggerConfigInterface $loggerConfig = null): LoggerInterface
            {
                return $this->logger;
            }
        };
        $this->factory->setConfig($this->configMock);
        $this->factory->setContainer($this->containerMock);
    }

    /**
     * @return void
     */
    public function testCreateQuoteCreator(): void
    {
        $this->containerMock->expects(static::atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('get')
            ->withConsecutive(
                [CompanyUserCartsRestApiDependencyProvider::FACADE_COMPANY_USER_REFERENCE],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
            )->willReturnOnConsecutiveCalls(
                $this->companyUserReferenceFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
                $this->persistentCartFacadeMock,
            );

        static::assertInstanceOf(
            QuoteCreator::class,
            $this->factory->createQuoteCreator(),
        );
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

    /**
     * @return void
     */
    public function testCreateQuoteDeleter(): void
    {
        $this->containerMock->expects(static::atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('get')
            ->withConsecutive(
                [CompanyUserCartsRestApiDependencyProvider::FACADE_QUOTE],
                [CompanyUserCartsRestApiDependencyProvider::FACADE_PERSISTENT_CART],
            )->willReturnOnConsecutiveCalls(
                $this->quoteFacadeMock,
                $this->persistentCartFacadeMock,
            );

        static::assertInstanceOf(
            QuoteDeleter::class,
            $this->factory->createQuoteDeleter(),
        );
    }

    /**
     * @return void
     */
    public function testCreateQuoteFinder(): void
    {
        $this->containerMock->expects(static::atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('get')
            ->with(CompanyUserCartsRestApiDependencyProvider::FACADE_QUOTE)
            ->willReturn($this->quoteFacadeMock);

        static::assertInstanceOf(
            QuoteFinder::class,
            $this->factory->createQuoteFinder(),
        );
    }
}
