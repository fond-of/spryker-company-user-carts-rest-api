<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartUpdater implements CartUpdaterInterface
{
    use SelfLinkCreatorTrait;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface
     */
    protected $cartReader;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected $restApiError;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface
     */
    protected $persistentCartClient;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected $cartsResourceMapper;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    protected $cartOperation;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartReaderInterface $cartReader
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface $cartOperation
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface $persistentCartClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface $cartsResourceMapper
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface $restApiError
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     */
    public function __construct(
        CartReaderInterface $cartReader,
        CartOperationInterface $cartOperation,
        CompanyUserCartsRestApiToPersistentCartClientInterface $persistentCartClient,
        CartsResourceMapperInterface $cartsResourceMapper,
        RestApiErrorInterface $restApiError,
        RestResourceBuilderInterface $restResourceBuilder
    ) {
        $this->cartReader = $cartReader;
        $this->cartOperation = $cartOperation;
        $this->persistentCartClient = $persistentCartClient;
        $this->cartsResourceMapper = $cartsResourceMapper;
        $this->restApiError = $restApiError;
        $this->restResourceBuilder = $restResourceBuilder;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function update(
        RestRequestInterface $restRequest,
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): RestResponseInterface {
        $restResponse = $this->restResourceBuilder->createRestResponse();

        $idCart = $this->findCartIdentifier($restRequest);

        if ($idCart === null) {
            return $this->restApiError->addRequiredParameterIsMissingError($restResponse);
        }

        $quoteResponseTransfer = $this->cartReader->getQuoteTransferByUuid($idCart, $restRequest);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->restApiError->addCartNotFoundError($restResponse);
        }

        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if ($quoteTransfer === null || $quoteTransfer->getIdQuote() === null) {
            return $this->restApiError->addCartNotFoundError($restResponse);
        }

        if ($restCartsRequestAttributesTransfer->getItems()->count() === 0) {
            return $this->createRestResponse($restRequest, $quoteTransfer);
        }

        $this->cartOperation->setQuoteTransfer($quoteTransfer)
            ->handleItems($restCartsRequestAttributesTransfer->getItems())
            ->reloadItems();

        return $this->createRestResponse($restRequest, $quoteTransfer);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createRestResponse(
        RestRequestInterface $restRequest,
        QuoteTransfer $quoteTransfer
    ): RestResponseInterface {
        $restResponse = $this->restResourceBuilder->createRestResponse();

        $cartsRestResource = $this->cartsResourceMapper->mapCartsResource(
            $quoteTransfer,
            $restRequest
        );

        $cartsRestResource->addLink(
            RestLinkInterface::LINK_SELF,
            $this->createSelfLink($quoteTransfer)
        );

        return $restResponse->addResource($cartsRestResource);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return string|null
     */
    protected function findCartIdentifier(RestRequestInterface $restRequest): ?string
    {
        $cartsResource = $restRequest->getResource();

        if ($cartsResource !== null) {
            return $cartsResource->getId();
        }

        return null;
    }
}
