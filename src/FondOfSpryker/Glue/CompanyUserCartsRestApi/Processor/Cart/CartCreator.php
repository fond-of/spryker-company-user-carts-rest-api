<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Client\PersistentCart\PersistentCartClientInterface;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Response;

class CartCreator implements CartCreatorInterface
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected $cartsResourceMapper;

    /**
     * @var \Spryker\Client\PersistentCart\PersistentCartClientInterface
     */
    protected $persistentCartClient;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface $cartsResourceMapper
     * @param \Spryker\Client\PersistentCart\PersistentCartClientInterface $persistentCartClient
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     */
    public function __construct(
        CartsResourceMapperInterface $cartsResourceMapper,
        PersistentCartClientInterface $persistentCartClient,
        RestResourceBuilderInterface $restResourceBuilder
    ) {
        $this->cartsResourceMapper = $cartsResourceMapper;
        $this->persistentCartClient = $persistentCartClient;
        $this->restResourceBuilder = $restResourceBuilder;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function create(RestRequestInterface $restRequest, RestCartsAttributesTransfer $restCartsAttributesTransfer): RestResponseInterface
    {
        $restResponse = $this->restResourceBuilder->createRestResponse();
        $quoteTransfer = $this->createQuoteTransfer($restCartsAttributesTransfer, $restRequest);
        $quoteResponseTransfer = $this->persistentCartClient->createQuote($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->createFailedCreatingCartError($quoteResponseTransfer, $restResponse);
        }

        $restResource = $this->cartsResourceMapper->mapCartsResource(
            $quoteResponseTransfer->getQuoteTransfer(),
            $restRequest
        );

        return $restResponse->addResource($restResource);
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(
        RestCartsAttributesTransfer $restCartsAttributesTransfer,
        RestRequestInterface $restRequest
    ): QuoteTransfer {
        $currencyTransfer = $this->getCurrencyTransfer($restCartsAttributesTransfer);
        $customerTransfer = $this->getCustomerTransfer($restRequest);
        $storeTransfer = $this->getStoreTransfer($restCartsAttributesTransfer);

        $quoteTransfer = (new QuoteTransfer())
            ->setCurrency($currencyTransfer)
            ->setCustomer($customerTransfer)
            ->setPriceMode($restCartsAttributesTransfer->getPriceMode())
            ->setStore($storeTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\CurrencyTransfer
     */
    protected function getCurrencyTransfer(RestCartsAttributesTransfer $restCartsAttributesTransfer): CurrencyTransfer
    {
        return (new CurrencyTransfer())
            ->setCode($restCartsAttributesTransfer->getCurrency());
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function getCustomerTransfer(RestRequestInterface $restRequest): CustomerTransfer
    {
        return (new CustomerTransfer())->setCustomerReference($restRequest->getUser()->getNaturalIdentifier());
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    protected function getStoreTransfer(RestCartsAttributesTransfer $restCartsAttributesTransfer): StoreTransfer
    {
        return (new StoreTransfer())
            ->setName($restCartsAttributesTransfer->getStore());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $response
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function createFailedCreatingCartError(QuoteResponseTransfer $quoteResponseTransfer, RestResponseInterface $response): RestResponseInterface
    {
        if ($quoteResponseTransfer->getErrors()->count() === 0) {
            return $response->addError($this->createRestErrorMessageTransfer(
                CartsRestApiConfig::RESPONSE_CODE_FAILED_CREATING_CART,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                CartsRestApiConfig::EXCEPTION_MESSAGE_FAILED_TO_CREATE_CART
            ));
        }

        foreach ($quoteResponseTransfer->getErrors() as $error) {
            if ($error->getMessage() === CartsRestApiConfig::EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_CART) {
                $response->addError($this->createRestErrorMessageTransfer(
                    CartsRestApiConfig::RESPONSE_CODE_CUSTOMER_ALREADY_HAS_CART,
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    CartsRestApiConfig::EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_CART
                ));

                continue;
            }

            $response->addError($this->createRestErrorMessageTransfer(
                CartsRestApiConfig::RESPONSE_CODE_FAILED_CREATING_CART,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $error->getMessage()
            ));
        }

        return $response;
    }

    /**
     * @param string $code
     * @param int $status
     * @param string $detail
     *
     * @return \Generated\Shared\Transfer\RestErrorMessageTransfer
     */
    protected function createRestErrorMessageTransfer(string $code, int $status, string $detail): RestErrorMessageTransfer
    {
        return (new RestErrorMessageTransfer())
            ->setCode($code)
            ->setStatus($status)
            ->setDetail($detail);
    }
}
