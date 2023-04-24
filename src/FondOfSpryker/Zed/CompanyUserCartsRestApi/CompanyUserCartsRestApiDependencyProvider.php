<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCartFacadeBridge;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCompanyUserReferenceFacadeBridge;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeBridge;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @codeCoverageIgnore
 */
class CompanyUserCartsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_CART = 'FACADE_CART';

    /**
     * @var string
     */
    public const FACADE_COMPANY_USER_REFERENCE = 'FACADE_COMPANY_USER_REFERENCE';

    /**
     * @var string
     */
    public const FACADE_PERMISSION = 'FACADE_PERMISSION';

    /**
     * @var string
     */
    public const FACADE_QUOTE = 'FACADE_QUOTE';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);

        $container = $this->addCartFacade($container);
        $container = $this->addCompanyUserReferenceFacade($container);
        $container = $this->addPermissionFacade($container);

        return $this->addQuoteFacade($container);
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCartFacade(Container $container): Container
    {
        $container[static::FACADE_CART] = static function (Container $container) {
            return new CompanyUserCartsRestApiToCartFacadeBridge(
                $container->getLocator()->cart()->facade(),
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCompanyUserReferenceFacade(Container $container): Container
    {
        $container[static::FACADE_COMPANY_USER_REFERENCE] = static function (Container $container) {
            return new CompanyUserCartsRestApiToCompanyUserReferenceFacadeBridge(
                $container->getLocator()->companyUserReference()->facade(),
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPermissionFacade(Container $container): Container
    {
        $container[static::FACADE_PERMISSION] = static function (Container $container) {
            return new CompanyUserCartsRestApiToPermissionFacadeBridge(
                $container->getLocator()->permission()->facade(),
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addQuoteFacade(Container $container): Container
    {
        $container[static::FACADE_QUOTE] = static function (Container $container) {
            return new CompanyUserCartsRestApiToQuoteFacadeBridge(
                $container->getLocator()->quote()->facade(),
            );
        };

        return $container;
    }
}
