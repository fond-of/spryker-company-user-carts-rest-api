<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

class CompanyUserCartsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    public const CLIENT_COMPANY_USER_QUOTE = 'CLIENT_COMPANY_USER_QUOTE';
    public const CLIENT_REST_API_COMPANY_USER = 'CLIENT_REST_API_COMPANY_USER';

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addCompanyUserQuoteClient($container);
        $container = $this->addCompanyUserRestApiClient($container);

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
            return $container->getLocator()->companyUserQuote()->client();
        };

        return $container;
    }

    /**
     * @param \Spryker\Glue\Kernel\Container $container
     *
     * @return \Spryker\Glue\Kernel\Container
     */
    protected function addCompanyUserRestApiClient(Container $container): Container
    {
        $container[static::CLIENT_REST_API_COMPANY_USER] = static function (Container $container) {
            return $container->getLocator()->companyUsersRestApi()->client();
        };

        return $container;
    }
}
