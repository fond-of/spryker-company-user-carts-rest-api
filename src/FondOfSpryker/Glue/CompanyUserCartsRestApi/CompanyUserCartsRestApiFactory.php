<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use FondOfSpryker\Client\CompanyUserQuote\CompanyUserQuoteClientInterface;
use FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReader;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
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
            $this->getCompanyUserQuoteClient(),
            $this->createCartsResourceMapper()
        );
    }

    /**
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected function createCartsResourceMapper(): CartsResourceMapperInterface
    {
        return new CartsResourceMapper(
            $this->createCartItemsResourceMapper(),
            $this->getResourceBuilder()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface
     */
    protected function createCartItemsResourceMapper(): CartItemsResourceMapperInterface
    {
        return new CartItemsResourceMapper();
    }

    /**
     * @return \FondOfSpryker\Client\CompanyUserQuote\CompanyUserQuoteClientInterface
     */
    protected function getCompanyUserQuoteClient(): CompanyUserQuoteClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_COMPANY_USER_QUOTE);
    }

    /**
     * @return \FondOfSpryker\Client\CompanyUsersRestApi\CompanyUsersRestApiClientInterface
     */
    protected function getCompanyUserRestApiClient(): CompanyUsersRestApiClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_REST_API_COMPANY_USER);
    }
}
