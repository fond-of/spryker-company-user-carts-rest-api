<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUsersRestApiClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface;
use FondOfSpryker\Glue\CompanyUsersRestApi\CompanyUsersRestApiConfig;
use Generated\Shared\Transfer\CompanyUserResponseTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Symfony\Component\HttpFoundation\Response;

class CartCreator implements CartCreatorInterface
{
    use SelfLinkCreatorTrait;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    protected $cartOperation;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface
     */
    protected $persistentCartClient;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface
     */
    protected $cartsResourceMapper;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUsersRestApiClientInterface
     */
    protected $companyUsersRestApiClient;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface
     */
    protected $restApiError;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface $cartOperation
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToPersistentCartClientInterface $persistentCartClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartsResourceMapperInterface $cartsResourceMapper
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCompanyUsersRestApiClientInterface $companyUsersRestApiClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation\RestApiErrorInterface $restApiError
     */
    public function __construct(
        CartOperationInterface $cartOperation,
        CompanyUserCartsRestApiToPersistentCartClientInterface $persistentCartClient,
        CartsResourceMapperInterface $cartsResourceMapper,
        RestResourceBuilderInterface $restResourceBuilder,
        CompanyUserCartsRestApiToCompanyUsersRestApiClientInterface $companyUsersRestApiClient,
        RestApiErrorInterface $restApiError
    ) {
        $this->cartOperation = $cartOperation;
        $this->persistentCartClient = $persistentCartClient;
        $this->cartsResourceMapper = $cartsResourceMapper;
        $this->restResourceBuilder = $restResourceBuilder;
        $this->companyUsersRestApiClient = $companyUsersRestApiClient;
        $this->restApiError = $restApiError;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function create(
        RestRequestInterface $restRequest,
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): RestResponseInterface {
        $companyUserResponseTransfer = $this->findCompanyUserByCompanyUserReference(
            $this->findCompanyUserIdentifier($restRequest)
        );

        if (!$companyUserResponseTransfer->getIsSuccessful()
            || !$this->isCompanyUserFromCurrentUser($restRequest, $companyUserResponseTransfer->getCompanyUser())
        ) {
            return $this->restApiError->addCompanyUserNotFoundErrorResponse(
                $this->restResourceBuilder->createRestResponse()
            );
        }

        $quoteTransfer = $this->createQuoteTransfer($restRequest, $restCartsRequestAttributesTransfer);

        $quoteResponseTransfer = $this->persistentCartClient->createQuote($quoteTransfer);

        if (!$quoteResponseTransfer->getIsSuccessful()) {
            return $this->createFailedCreatingCartError($quoteResponseTransfer);
        }

        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if ($restCartsRequestAttributesTransfer->getItems()->count() === 0) {
            return $this->createRestResponse($restRequest, $quoteTransfer);
        }

        $this->cartOperation->setQuoteTransfer($quoteTransfer)
            ->handleItems($restCartsRequestAttributesTransfer->getItems());

        return $this->createRestResponse($restRequest, $quoteTransfer);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\CompanyUserTransfer $companyUserTransfer
     *
     * @return bool
     */
    protected function isCompanyUserFromCurrentUser(
        RestRequestInterface $restRequest,
        CompanyUserTransfer $companyUserTransfer
    ): bool {
        if ($restRequest->getRestUser() === null) {
            return false;
        }

        $test = $restRequest->getRestUser()->getSurrogateIdentifier() === $companyUserTransfer->getFkCustomer();

        return $test;
    }

    /**
     * @param string|null $companyUserIdentifier
     *
     * @return \Generated\Shared\Transfer\CompanyUserResponseTransfer
     */
    protected function findCompanyUserByCompanyUserReference(?string $companyUserIdentifier): CompanyUserResponseTransfer
    {
        $companyUserTransfer = (new CompanyUserTransfer())
            ->setCompanyUserReference($companyUserIdentifier);

        return $this->companyUsersRestApiClient
            ->findCompanyUserByCompanyUserReference($companyUserTransfer);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(
        RestRequestInterface $restRequest,
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): QuoteTransfer {
        $customerReference = $this->findCustomerIdentifier($restRequest);
        $companyUserReference = $this->findCompanyUserIdentifier($restRequest);

        $quoteTransfer = $this->cartsResourceMapper
            ->mapRestCartsRequestAttributesTransferToQuoteTransfer($restCartsRequestAttributesTransfer);

        return $quoteTransfer->setCustomer((new CustomerTransfer())->setCustomerReference($customerReference))
            ->setCompanyUser((new CompanyUserTransfer())->setCompanyUserReference($companyUserReference))
            ->setCustomerReference($customerReference)
            ->setCompanyUserReference($companyUserReference);
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
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return string|null
     */
    protected function findCustomerIdentifier(RestRequestInterface $restRequest): ?string
    {
        $restUserTransfer = $restRequest->getRestUser();

        if ($restUserTransfer !== null) {
            return $restUserTransfer->getNaturalIdentifier();
        }

        return null;
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
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    protected function createFailedCreatingCartError(
        QuoteResponseTransfer $quoteResponseTransfer
    ): RestResponseInterface {
        $restResponse = $this->restResourceBuilder->createRestResponse();

        if ($quoteResponseTransfer->getErrors()->count() === 0) {
            return $restResponse->addError($this->createRestErrorMessageTransfer(
                CartsRestApiConfig::RESPONSE_CODE_FAILED_CREATING_CART,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                CartsRestApiConfig::EXCEPTION_MESSAGE_FAILED_TO_CREATE_CART
            ));
        }

        foreach ($quoteResponseTransfer->getErrors() as $error) {
            if ($error->getMessage() === CartsRestApiConfig::EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_CART) {
                $restResponse->addError($this->createRestErrorMessageTransfer(
                    CartsRestApiConfig::RESPONSE_CODE_CUSTOMER_ALREADY_HAS_CART,
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    CartsRestApiConfig::EXCEPTION_MESSAGE_CUSTOMER_ALREADY_HAS_CART
                ));

                continue;
            }

            $restResponse->addError($this->createRestErrorMessageTransfer(
                CartsRestApiConfig::RESPONSE_CODE_FAILED_CREATING_CART,
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $error->getMessage()
            ));
        }

        return $restResponse;
    }

    /**
     * @param string $code
     * @param int $status
     * @param string $detail
     *
     * @return \Generated\Shared\Transfer\RestErrorMessageTransfer
     */
    protected function createRestErrorMessageTransfer(
        string $code,
        int $status,
        string $detail
    ): RestErrorMessageTransfer {
        return (new RestErrorMessageTransfer())
            ->setCode($code)
            ->setStatus($status)
            ->setDetail($detail);
    }
}
