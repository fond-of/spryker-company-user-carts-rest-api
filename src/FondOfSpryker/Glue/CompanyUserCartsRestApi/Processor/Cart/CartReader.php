<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use FondOfSpryker\Glue\CompanyUsersRestApi\CompanyUsersRestApiConfig;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;
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
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected $restApiError;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface $cartOperation
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUserQuoteClientInterface $companyUserQuoteClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface $cartsResourceMapper
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface $restApiError
     */
    public function __construct(
        CartOperationInterface $cartOperation,
        CompanyUserCartsRestApiToCompanyUserQuoteClientInterface $companyUserQuoteClient,
        CartsResourceMapperInterface $cartsResourceMapper,
        RestResourceBuilderInterface $restResourceBuilder,
        RestApiErrorInterface $restApiError
    ) {
        $this->cartOperation = $cartOperation;
        $this->companyUserQuoteClient = $companyUserQuoteClient;
        $this->cartsResourceMapper = $cartsResourceMapper;
        $this->restResourceBuilder = $restResourceBuilder;
        $this->restApiError = $restApiError;
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
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function readCompanyUserCartByUuid(RestRequestInterface $restRequest): RestResponseInterface
    {
        $restResponse = $this->restResourceBuilder->createRestResponse();

        $quoteResponseTransfer = $this->getQuoteTransferByUuid(
            $restRequest->getResource()->getId(),
            $restRequest
        );

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->restApiError->addCartNotFoundError($restResponse);
        }

        $quoteTransfer = $this->prepareQuote($quoteResponseTransfer->getQuoteTransfer());

        $this->cartOperation->setQuoteTransfer($quoteTransfer);

        $cartResource = $this->cartsResourceMapper->mapCartsResource($quoteTransfer, $restRequest);

        $cartResource->addLink(
            RestLinkInterface::LINK_SELF,
            $this->createSelfLink($quoteTransfer)
        );

        return $restResponse->addResource($cartResource);
    }

    /**
     * @param string $uuidCart
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|null
     */
    public function getQuoteTransferByUuid(string $uuidCart, RestRequestInterface $restRequest): ?QuoteTransfer
    {
        $quoteCollectionTransfer = $this->getCustomerCompanyUserQuote($uuidCart, $restRequest);

        if ($quoteCollectionTransfer->getQuotes()->count() !== 1) {
            return null;
        }

        $quoteTransfer = $quoteCollectionTransfer->getQuotes()
            ->offsetGet(0);

        return $this->prepareQuote($quoteTransfer);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected function getCustomerCompanyUserQuotes(RestRequestInterface $restRequest): QuoteCollectionTransfer
    {
        $quoteCriteriaFilterTransfer = new QuoteCriteriaFilterTransfer();
        $quoteCriteriaFilterTransfer->setCustomerReference($restRequest->getUser()->getNaturalIdentifier());
        $quoteCriteriaFilterTransfer->setCompanyUserReference($this->findCompanyUserIdentifier($restRequest));

        $quoteCollectionTransfer = $this->companyUserQuoteClient->getCompanyUserQuoteCollectionByCriteria($quoteCriteriaFilterTransfer);

        return $quoteCollectionTransfer;
    }

    /**
     * @param string $uuid
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    protected function getCustomerCompanyUserQuote(string $uuid, RestRequestInterface $restRequest): QuoteCollectionTransfer
    {
        $quoteCriteriaFilterTransfer = new QuoteCriteriaFilterTransfer();
        $quoteCriteriaFilterTransfer->setCustomerReference($restRequest->getUser()->getNaturalIdentifier());
        $quoteCriteriaFilterTransfer->setCompanyUserReference($this->findCompanyUserIdentifier($restRequest));
        $quoteCriteriaFilterTransfer->setUuid($uuid);

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
