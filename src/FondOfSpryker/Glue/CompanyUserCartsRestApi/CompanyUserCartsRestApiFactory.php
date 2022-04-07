<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserReferenceClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilder;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartDeleter;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartDeleterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperation;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReader;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Creator\CartCreator;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Creator\CartCreatorInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpander;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpanderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CompanyUserReferenceFilter;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CompanyUserReferenceFilterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CustomerReferenceFilter;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CustomerReferenceFilterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCartFilter;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCartFilterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCustomerFilter;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCustomerFilterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsAttributesMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsAttributesMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsDiscountsMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsDiscountsMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsTotalsMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsTotalsMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestItemsAttributesMapper;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestItemsAttributesMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Updater\CartUpdater;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Updater\CartUpdaterInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiError;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
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
            $this->getResourceBuilder(),
            $this->createRestApiError(),
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
            $this->getResourceBuilder(),
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
            $this->getConfig(),
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
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface
     */
    protected function getCompanyUserQuoteClient(): CompanyUserCartsRestApiToCompanyUserQuoteClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_COMPANY_USER_QUOTE);
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface
     */
    protected function getPersistentCartClient(): CompanyUserCartsRestApiToPersistentCartClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_PERSISTENT_CART);
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface
     */
    protected function getCartClient(): CompanyUserCartsRestApiToCartClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_CART);
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface
     */
    protected function getQuoteClient(): CompanyUserCartsRestApiToQuoteClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserReferenceClientInterface
     */
    protected function getCompanyUserReferenceClient(): CompanyUserCartsRestApiToCompanyUserReferenceClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_COMPANY_USER_REFERENCE);
    }

    /**
     * @return \Spryker\Glue\CartsRestApi\CartsRestApiConfig
     */
    protected function getCartsRestApiConfig(): CartsRestApiConfig
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CART_REST_API_CONFIG);
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
            $this->getRestCartItemExpanderPlugins(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Updater\CartUpdater
     */
    public function createCartUpdater(): CartUpdaterInterface
    {
        return new CartUpdater(
            $this->createRestCompanyUserCartsRequestMapper(),
            $this->createRestCartItemExpander(),
            $this->createRestResponseBuilder(),
            $this->getClient(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Creator\CartCreatorInterface
     */
    public function createCartCreator(): CartCreatorInterface
    {
        return new CartCreator(
            $this->createRestCompanyUserCartsRequestMapper(),
            $this->createRestCartItemExpander(),
            $this->createRestResponseBuilder(),
            $this->getClient(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapperInterface
     */
    protected function createRestCompanyUserCartsRequestMapper(): RestCompanyUserCartsRequestMapperInterface
    {
        return new RestCompanyUserCartsRequestMapper(
            $this->createIdCartFilter(),
            $this->createCompanyUserReferenceFilter(),
            $this->createCustomerReferenceFilter(),
            $this->createIdCustomerFilter(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCartFilterInterface
     */
    protected function createIdCartFilter(): IdCartFilterInterface
    {
        return new IdCartFilter();
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CompanyUserReferenceFilterInterface
     */
    protected function createCompanyUserReferenceFilter(): CompanyUserReferenceFilterInterface
    {
        return new CompanyUserReferenceFilter();
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\CustomerReferenceFilterInterface
     */
    protected function createCustomerReferenceFilter(): CustomerReferenceFilterInterface
    {
        return new CustomerReferenceFilter();
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Filter\IdCustomerFilterInterface
     */
    protected function createIdCustomerFilter(): IdCustomerFilterInterface
    {
        return new IdCustomerFilter();
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpanderInterface
     */
    protected function createRestCartItemExpander(): RestCartItemExpanderInterface
    {
        return new RestCartItemExpander(
            $this->getRestCartItemExpanderPlugins(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface
     */
    protected function createRestResponseBuilder(): RestResponseBuilderInterface
    {
        return new RestResponseBuilder(
            $this->createRestCartsAttributesMapper(),
            $this->createRestItemsAttributesMapper(),
            $this->getResourceBuilder(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsAttributesMapperInterface
     */
    protected function createRestCartsAttributesMapper(): RestCartsAttributesMapperInterface
    {
        return new RestCartsAttributesMapper(
            $this->createRestCartsDiscountsMapper(),
            $this->createRestCartsTotalsMapper(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsDiscountsMapperInterface
     */
    protected function createRestCartsDiscountsMapper(): RestCartsDiscountsMapperInterface
    {
        return new RestCartsDiscountsMapper();
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsTotalsMapperInterface
     */
    protected function createRestCartsTotalsMapper(): RestCartsTotalsMapperInterface
    {
        return new RestCartsTotalsMapper();
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestItemsAttributesMapperInterface
     */
    protected function createRestItemsAttributesMapper(): RestItemsAttributesMapperInterface
    {
        return new RestItemsAttributesMapper();
    }

    /**
     * @return array<\FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface>
     */
    protected function getRestCartItemExpanderPlugins(): array
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::PLUGINS_REST_CART_ITEM_EXPANDER);
    }
}
