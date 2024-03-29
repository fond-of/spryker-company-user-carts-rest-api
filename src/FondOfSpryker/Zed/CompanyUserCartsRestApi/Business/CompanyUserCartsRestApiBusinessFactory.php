<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdder;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizer;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker\ReadPermissionChecker;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker\ReadPermissionCheckerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker\WritePermissionChecker;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker\WritePermissionCheckerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator\QuoteCreator;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator\QuoteCreatorInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter\QuoteDeleter;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter\QuoteDeleterInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpander;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinder;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinder;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Grouper\ItemsGrouper;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Grouper\ItemsGrouperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandler;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapper;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapper;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReader;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReader;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemover;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdater;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdaterInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiDependencyProvider;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCartFacadeInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCompanyUserReferenceFacadeInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig getConfig()
 */
class CompanyUserCartsRestApiBusinessFactory extends AbstractBusinessFactory
{
    use LoggerTrait;

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Creator\QuoteCreatorInterface
     */
    public function createQuoteCreator(): QuoteCreatorInterface
    {
        return new QuoteCreator(
            $this->createCompanyUserReader(),
            $this->createQuoteMapper(),
            $this->createQuoteHandler(),
            $this->createQuoteFinder(),
            $this->createWritePermissionChecker(),
            $this->getQuoteFacade(),
            $this->getLogger(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\QuoteUpdaterInterface
     */
    public function createQuoteUpdater(): QuoteUpdaterInterface
    {
        return new QuoteUpdater(
            $this->createQuoteFinder(),
            $this->createQuoteExpander(),
            $this->createQuoteHandler(),
            $this->createWritePermissionChecker(),
            $this->getQuoteFacade(),
            $this->getLogger(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Deleter\QuoteDeleterInterface
     */
    public function createQuoteDeleter(): QuoteDeleterInterface
    {
        return new QuoteDeleter(
            $this->createQuoteReader(),
            $this->createQuoteExpander(),
            $this->createWritePermissionChecker(),
            $this->getQuoteFacade(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\QuoteFinderInterface
     */
    public function createQuoteFinder(): QuoteFinderInterface
    {
        return new QuoteFinder(
            $this->createQuoteReader(),
            $this->createReadPermissionChecker(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\QuoteReaderInterface
     */
    protected function createQuoteReader(): QuoteReaderInterface
    {
        return new QuoteReader(
            $this->getQuoteFacade(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\QuoteMapperInterface
     */
    protected function createQuoteMapper(): QuoteMapperInterface
    {
        return new QuoteMapper();
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander\QuoteExpanderInterface
     */
    protected function createQuoteExpander(): QuoteExpanderInterface
    {
        return new QuoteExpander(
            $this->getConfig(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler\QuoteHandlerInterface
     */
    protected function createQuoteHandler(): QuoteHandlerInterface
    {
        return new QuoteHandler(
            $this->createItemsCategorizer(),
            $this->createItemAdder(),
            $this->createItemRemover(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface
     */
    protected function createItemsCategorizer(): ItemsCategorizerInterface
    {
        return new ItemsCategorizer(
            $this->createItemMapper(),
            $this->createItemFinder(),
            $this->createItemGrouper(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapperInterface
     */
    protected function createItemMapper(): ItemMapperInterface
    {
        return new ItemMapper();
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinderInterface
     */
    protected function createItemFinder(): ItemFinderInterface
    {
        return new ItemFinder();
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Grouper\ItemsGrouperInterface
     */
    protected function createItemGrouper(): ItemsGrouperInterface
    {
        return new ItemsGrouper();
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface
     */
    protected function createItemAdder(): ItemAdderInterface
    {
        return new ItemAdder($this->getCartFacade());
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface
     */
    protected function createItemRemover(): ItemRemoverInterface
    {
        return new ItemRemover($this->getCartFacade());
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface
     */
    protected function getQuoteFacade(): CompanyUserCartsRestApiToQuoteFacadeInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::FACADE_QUOTE);
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader\CompanyUserReaderInterface
     */
    protected function createCompanyUserReader(): CompanyUserReaderInterface
    {
        return new CompanyUserReader($this->getCompanyUserReferenceFacade());
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker\WritePermissionCheckerInterface
     */
    protected function createWritePermissionChecker(): WritePermissionCheckerInterface
    {
        return new WritePermissionChecker(
            $this->createCompanyUserReader(),
            $this->getPermissionFacade(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Checker\ReadPermissionCheckerInterface
     */
    protected function createReadPermissionChecker(): ReadPermissionCheckerInterface
    {
        return new ReadPermissionChecker(
            $this->createCompanyUserReader(),
            $this->getPermissionFacade(),
        );
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCompanyUserReferenceFacadeInterface
     */
    protected function getCompanyUserReferenceFacade(): CompanyUserCartsRestApiToCompanyUserReferenceFacadeInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::FACADE_COMPANY_USER_REFERENCE);
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToCartFacadeInterface
     */
    protected function getCartFacade(): CompanyUserCartsRestApiToCartFacadeInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::FACADE_CART);
    }

    /**
     * @return \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPermissionFacadeInterface
     */
    protected function getPermissionFacade(): CompanyUserCartsRestApiToPermissionFacadeInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::FACADE_PERMISSION);
    }
}
