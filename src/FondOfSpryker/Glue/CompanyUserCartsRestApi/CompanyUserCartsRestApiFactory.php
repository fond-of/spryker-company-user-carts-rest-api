<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReader;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface;
use Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\QuoteCollectionReaderPluginInterface;
use Spryker\Glue\Kernel\AbstractFactory;

class CompanyUserCartsRestApiFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface
     */
    public function createCartReader(): CartReaderInterface
    {
        return new CartReader(
            $this->getResourceBuilder(),
            $this->getCompanyUserRestApiClient()
        );
    }

    /**
     * @return \Spryker\Glue\CartsRestApiExtension\Dependency\Plugin\QuoteCollectionReaderPluginInterface
     */
    protected function getCartQuoteCollectionReaderPlugin(): QuoteCollectionReaderPluginInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::PLUGIN_QUOTE_COLLECTION_READER);
    }


    /**
     * @return \FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface
     */
    protected function getCompanyUserRestApiClient(): CompanyUsersRestApiClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_REST_API_COMPANY_USER);
    }
}
