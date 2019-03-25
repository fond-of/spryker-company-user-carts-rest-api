<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\CartItems;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToZedRequestClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Response;

class CartItemsPersister implements CartItemsPersisterInterface
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToCartClientInterface
     */
    protected $cartClient;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface
     */
    protected $cartItemsResourceMapper;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface $cartItemsResourceMapper
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToCartClientInterface $cartClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToQuoteClientInterface $quoteClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CartItemsRestApiToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(
        RestResourceBuilderInterface $restResourceBuilder,
        CartItemsResourceMapperInterface $cartItemsResourceMapper,
        CartItemsRestApiToCartClientInterface $cartClient,
        CartItemsRestApiToQuoteClientInterface $quoteClient,
        CartItemsRestApiToZedRequestClientInterface $zedRequestClient
    ) {

        $this->cartClient = $cartClient;
        $this->cartItemsResourceMapper = $cartItemsResourceMapper;
        $this->restResourceBuilder = $restResourceBuilder;
        $this->quoteClient = $quoteClient;
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function persist(
        RestRequestInterface $restRequest,
        RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
    ): RestResponseInterface {
        $restResponse = $this->restResourceBuilder->createRestResponse();

        $idCart = $this->findCartIdentifier($restRequest);
        if ($idCart === null) {
            return $this->createCartIdMissingError();
        }

        $quoteResponseTransfer = $this->cartReader->getQuoteTransferByUuid($idCart, $restRequest);
        if (!$quoteResponseTransfer->getIsSuccessful() || $quoteResponseTransfer->getQuoteTransfer() === null) {
            return $this->createCartNotFoundError();
        }

        $this->quoteClient->setQuote($quoteResponseTransfer->getQuoteTransfer());
        $quoteTransfer = $this->cartClient->addItems(
            $this->cartItemsResourceMapper->mapCartItemsRequestAttributesToItems($restCartItemsRequestAttributesTransfer)
        );

        $errors = $this->zedRequestClient->getLastResponseErrorMessages();
        if (count($errors) > 0) {
            return $this->returnWithError($errors, $restResponse);
        }

        return $this->cartReader->readByIdentifier($quoteTransfer->getUuid(), $restRequest);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return string|null
     */
    protected function findCartIdentifier(RestRequestInterface $restRequest): ?string
    {
        $cartsResource = $restRequest->findParentResourceByType(CartsRestApiConfig::RESOURCE_CARTS);
        if ($cartsResource !== null) {
            return $cartsResource->getId();
        }

        return null;
    }

    /**
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function createCartIdMissingError(): RestResponseInterface
    {
        $restErrorTransfer = (new RestErrorMessageTransfer())
            ->setCode(CartsRestApiConfig::RESPONSE_CODE_CART_ID_MISSING)
            ->setStatus(Response::HTTP_BAD_REQUEST)
            ->setDetail(CartsRestApiConfig::EXCEPTION_MESSAGE_CART_ID_MISSING);

        return $this->restResourceBuilder->createRestResponse()->addError($restErrorTransfer);
    }
}
