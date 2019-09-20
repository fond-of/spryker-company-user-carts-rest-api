<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientBridge;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientBridge;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientBridge;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientBridge;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

class CompanyUserCartsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_COMPANY_USER_QUOTE = 'CLIENT_COMPANY_USER_QUOTE';
    public const CLIENT_PERSISTENT_CART = 'CLIENT_PERSISTENT_CART';
    public const CLIENT_CART = 'CLIENT_CART';
    public const CLIENT_QUOTE = 'CLIENT_QUOTE';
    public const CLIENT_COMPANY_USER_REST_API = 'CLIENT_COMPANY_USER_REST_API';
    public const CART_REST_API_CONFIG = 'CART_REST_API_CONFIG';
    public const PLUGINS_REST_CART_ITEM_EXPANDER = 'PLUGINS_REST_CART_ITEM_EXPANDER';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addCompanyUserQuoteClient($container);
        $container = $this->addQuoteClient($container);
        $container = $this->addCartsRestApiConfig($container);
        $container = $this->addPersistentCartClient($container);
        $container = $this->addCartClient($container);
        $container = $this->addRestCartItemExpanderPlugins($container);
        $container = $this->addCompanyUserRestApiClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCompanyUserRestApiClient(Container $container): Container
    {
        $container[static::CLIENT_COMPANY_USER_REST_API] = static function (Container $container) {
            return $container->getLocator()->companyUsersRestApi()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCompanyUserQuoteClient(Container $container): Container
    {
        $container[static::CLIENT_COMPANY_USER_QUOTE] = static function (Container $container) {
            return new CompanyUserCartsRestApiToCompanyUserQuoteClientBridge(
                $container->getLocator()->companyUserQuote()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addPersistentCartClient(Container $container): Container
    {
        $container[static::CLIENT_PERSISTENT_CART] = static function (Container $container) {
            return new CompanyUserCartsRestApiToPersistentCartClientBridge(
                $container->getLocator()->persistentCart()->client()
            );
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCartClient(Container $container): Container
    {
        $container[static::CLIENT_CART] = static function (Container $container) {
            return new CompanyUserCartsRestApiToCartClientBridge($container->getLocator()->cart()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addQuoteClient(Container $container): Container
    {
        $container[static::CLIENT_QUOTE] = static function (Container $container) {
            return new CompanyUserCartsRestApiToQuoteClientBridge($container->getLocator()->quote()->client());
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCartsRestApiConfig(Container $container): Container
    {
        $container[static::CART_REST_API_CONFIG] = static function () {
            return new CartsRestApiConfig();
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addRestCartItemExpanderPlugins(Container $container): Container
    {
        $container[static::PLUGINS_REST_CART_ITEM_EXPANDER] = function () {
            return $this->getRestCartItemExpanderPlugins();
        };

        return $container;
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface[]
     */
    protected function getRestCartItemExpanderPlugins(): array
    {
        return [];
    }
}
