<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartCreator;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartCreatorInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartDeleter;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartDeleterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperation;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReader;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartUpdater;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartUpdaterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiError;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use Spryker\Glue\Kernel\AbstractFactory;

/**
 * @method \FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig getConfig()
 */
class CompanyUserCartsRestApiFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface
     */
    public function createCartReader(): CartReaderInterface
    {
        return new CartReader(
            $this->createCartOperation(),
            $this->getCompanyUserQuoteClient(),
            $this->createCartsResourceMapper(),
            $this->getResourceBuilder()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartDeleterInterface
     */
    public function createCartDeleter(): CartDeleterInterface
    {
        return new CartDeleter(
            $this->createCartReader(),
            $this->getPersistentCartClient(),
            $this->getResourceBuilder()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartCreatorInterface
     */
    public function createCartCreator(): CartCreatorInterface
    {
        return new CartCreator(
            $this->createCartOperation(),
            $this->getPersistentCartClient(),
            $this->createCartsResourceMapper(),
            $this->getResourceBuilder()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartUpdaterInterface
     */
    public function createCartUpdater(): CartUpdaterInterface
    {
        return new CartUpdater(
            $this->createCartReader(),
            $this->createCartOperation(),
            $this->getPersistentCartClient(),
            $this->createCartsResourceMapper(),
            $this->createRestApiError(),
            $this->getResourceBuilder()
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected function createCartsResourceMapper(): CartsResourceMapperInterface
    {
        return new CartsResourceMapper(
            $this->createCartItemsResourceMapper(),
            $this->getResourceBuilder(),
            $this->getConfig()->getAllowedFieldsToPatchInQuote()
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
     * @throws
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface
     */
    protected function getCompanyUserQuoteClient(): CompanyUserCartsRestApiToCompanyUserQuoteClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_COMPANY_USER_QUOTE);
    }

    /**
     * @throws
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface
     */
    protected function getPersistentCartClient(): CompanyUserCartsRestApiToPersistentCartClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_PERSISTENT_CART);
    }

    /**
     * @throws
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface
     */
    protected function getCartClient(): CompanyUserCartsRestApiToCartClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_CART);
    }

    /**
     * @throws
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface
     */
    protected function getQuoteClient(): CompanyUserCartsRestApiToQuoteClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected function createRestApiError(): RestApiErrorInterface
    {
        return new RestApiError();
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    protected function createCartOperation(): CartOperationInterface
    {
        return new CartOperation(
            $this->getCartClient(),
            $this->getQuoteClient(),
            $this->createCartItemsResourceMapper(),
            $this->getRestCartItemExpanderPlugins()
        );
    }

    /**
     * @throws
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface[]
     */
    protected function getRestCartItemExpanderPlugins(): array
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::PLUGINS_REST_CART_ITEM_EXPANDER);
    }
}
