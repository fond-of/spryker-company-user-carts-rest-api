<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class RestApiError implements RestApiErrorInterface
{
    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addCouldNotUpdateCartError(RestResponseInterface $restResponse): RestResponseInterface
    {
        $restErrorMessageTransfer = (new RestErrorMessageTransfer())
            ->setCode(CompanyUserCartsRestApiConfig::RESPONSE_CODE_COULD_NOT_UPDATE_CART)
            ->setStatus(Response::HTTP_NOT_FOUND)
            ->setDetail(CompanyUserCartsRestApiConfig::RESPONSE_MESSAGE_COULD_NOT_UPDATE_CART);

        return $restResponse->addError($restErrorMessageTransfer);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addCartNotFoundError(RestResponseInterface $restResponse): RestResponseInterface
    {
        $restErrorMessageTransfer = (new RestErrorMessageTransfer())
            ->setCode(CompanyUserCartsRestApiConfig::RESPONSE_CODE_CART_NOT_FOUND)
            ->setStatus(Response::HTTP_NOT_FOUND)
            ->setDetail(CompanyUserCartsRestApiConfig::RESPONSE_MESSAGE_CART_NOT_FOUND);

        return $restResponse->addError($restErrorMessageTransfer);
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addRequiredParameterIsMissingError(RestResponseInterface $restResponse): RestResponseInterface
    {
        $restErrorMessageTransfer = (new RestErrorMessageTransfer())
            ->setCode(CompanyUserCartsRestApiConfig::RESPONSE_CODE_REQUIRED_PARAMETER_IS_MISSING)
            ->setStatus(Response::HTTP_BAD_REQUEST)
            ->setDetail(CompanyUserCartsRestApiConfig::RESPONSE_MESSAGE_REQUIRED_PARAMETER_IS_MISSING);

        return $restResponse->addError($restErrorMessageTransfer);
    }
}
