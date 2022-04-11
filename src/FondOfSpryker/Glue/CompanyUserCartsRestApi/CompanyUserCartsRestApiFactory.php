<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilder;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Creator\CartCreator;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Creator\CartCreatorInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Deleter\CartDeleter;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Deleter\CartDeleterInterface;
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
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Finder\CartFinder;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Finder\CartFinderInterface;
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
use Spryker\Glue\Kernel\AbstractFactory;

/**
 * @method \FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig getConfig()
 * @method \FondOfSpryker\Client\CompanyUserCartsRestApi\CompanyUserCartsRestApiClient getClient()
 */
class CompanyUserCartsRestApiFactory extends AbstractFactory
{
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
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Deleter\CartDeleterInterface
     */
    public function createCartDeleter(): CartDeleterInterface
    {
        return new CartDeleter(
            $this->createRestCompanyUserCartsRequestMapper(),
            $this->createRestResponseBuilder(),
            $this->getClient(),
        );
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Finder\CartFinderInterface
     */
    public function createCartFinder(): CartFinderInterface
    {
        return new CartFinder(
            $this->createRestCompanyUserCartsRequestMapper(),
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
