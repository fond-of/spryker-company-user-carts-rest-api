<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUsersRestApi\CompanyUsersRestApiConfig;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartReader implements CartReaderInterface
{
    use SelfLinkCreatorTrait;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    protected $cartOperation;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface
     */
    protected $companyUserQuoteClient;

    /**
     * @var \Spryker\Glue\CartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected $cartsResourceMapper;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface $cartOperation
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface $companyUserQuoteClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface $cartsResourceMapper
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     */
    public function __construct(
        CartOperationInterface $cartOperation,
        CompanyUserCartsRestApiToCompanyUserQuoteClientInterface $companyUserQuoteClient,
        CartsResourceMapperInterface $cartsResourceMapper,
        RestResourceBuilderInterface $restResourceBuilder
    ) {
        $this->cartOperation = $cartOperation;
        $this->companyUserQuoteClient = $companyUserQuoteClient;
        $this->cartsResourceMapper = $cartsResourceMapper;
        $this->restResourceBuilder = $restResourceBuilder;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function readCurrentCompanyUserCarts(RestRequestInterface $restRequest): RestResponseInterface
    {
        $quoteCollectionTransfer = $this->getCustomerCompanyUserQuotes($restRequest);

        $restResponse = $this->restResourceBuilder->createRestResponse(count($quoteCollectionTransfer->getQuotes()));
        if (count($quoteCollectionTransfer->getQuotes()) === 0) {
            return $restResponse;
        }

        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            $quoteTransfer = $this->prepareQuote($quoteTransfer);

            $this->cartOperation->setQuoteTransfer($quoteTransfer);

            $cartResource = $this->cartsResourceMapper->mapCartsResource($quoteTransfer, $restRequest);

            $cartResource->addLink(
                RestLinkInterface::LINK_SELF,
                $this->createSelfLink($quoteTransfer)
            );

            $restResponse->addResource($cartResource);
        }

        return $restResponse;
    }

    /**
     * @param string $uuidCart
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function getQuoteTransferByUuid(string $uuidCart, RestRequestInterface $restRequest): QuoteResponseTransfer
    {
        $quoteCollectionTransfer = $this->getCustomerCompanyUserQuotes($restRequest);

        if ($quoteCollectionTransfer->getQuotes()->count() === 0) {
            return (new QuoteResponseTransfer())
                ->setIsSuccessful(false);
        }

        foreach ($quoteCollectionTransfer->getQuotes() as $quoteTransfer) {
            if ($quoteTransfer->getUuid() === $uuidCart) {
                $this->prepareQuote($quoteTransfer);

                return (new QuoteResponseTransfer())
                    ->setIsSuccessful(true)
                    ->setQuoteTransfer($quoteTransfer);
            }
        }

        return (new QuoteResponseTransfer())
            ->setIsSuccessful(false);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function getCustomerCompanyUserQuotes(RestRequestInterface $restRequest): QuoteCollectionTransfer
    {
        $quoteCriteriaFilterTransfer = new QuoteCriteriaFilterTransfer();
        $quoteCriteriaFilterTransfer->setCustomerReference($restRequest->getUser()->getNaturalIdentifier());
        $quoteCriteriaFilterTransfer->setCompanyUserReference($this->findCompanyUserIdentifier($restRequest));

        $quoteCollectionTransfer = $this->companyUserQuoteClient->getCompanyUserQuoteCollectionByCriteria($quoteCriteriaFilterTransfer);

        return $quoteCollectionTransfer;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return string|null
     */
    protected function findCompanyUserIdentifier(RestRequestInterface $restRequest): ?string
    {
        $companyUsersResource = $restRequest->findParentResourceByType(CompanyUsersRestApiConfig::RESOURCE_COMPANY_USERS);
        if ($companyUsersResource !== null) {
            return $companyUsersResource->getId();
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function prepareQuote(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getCustomer() === null && $quoteTransfer->getCustomerReference() !== null) {
            $customerTransfer = (new CustomerTransfer())->setCustomerReference($quoteTransfer->getCustomerReference());

            $quoteTransfer->setCustomer($customerTransfer);
        }

        if ($quoteTransfer->getCompanyUser() === null && $quoteTransfer->getCompanyUserReference() !== null) {
            $companyUserTransfer = (new CompanyUserTransfer())
                ->setCompanyUserReference($quoteTransfer->getCompanyUserReference());

            $quoteTransfer->setCompanyUser($companyUserTransfer);
        }

        return $quoteTransfer;
    }
}
