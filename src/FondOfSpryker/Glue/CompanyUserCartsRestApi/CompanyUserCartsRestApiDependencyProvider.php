<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use FondOfSpryker\Glue\PersistentCartsRestApi\Plugin\CartsRestApiExtension\CartQuoteCollectionReaderPlugin;
use Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\QuoteCollectionReaderPluginInterface;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

class CompanyUserCartsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_REST_API_COMPANY_USER = 'CLIENT_REST_API_COMPANY_USER';
    public const PLUGIN_QUOTE_COLLECTION_READER = 'PLUGIN_QUOTE_COLLECTION_READER';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addQuoteCollectionReaderPlugin($container);
        $container = $this->addCompanyUserRestApiClient($container);

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addQuoteCollectionReaderPlugin(Container $container): Container
    {
        $container[static::PLUGIN_QUOTE_COLLECTION_READER] = function () {
            return $this->getQuoteCollectionReaderPlugin();
        };

        return $container;
    }

    /**
     * @return \Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\QuoteCollectionReaderPluginInterface
     */
    protected function getQuoteCollectionReaderPlugin(): QuoteCollectionReaderPluginInterface
    {
        return new CartQuoteCollectionReaderPlugin();
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCompanyUserRestApiClient(Container $container): Container
    {
        $container[static::CLIENT_REST_API_COMPANY_USER] = function (Container $container) {
            return $container->getLocator()->companyUsersRestApi()->client();
        };

        return $container;
    }
}
