<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Validation;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface RestApiErrorInterface
{
    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addCompanyUserNotFoundErrorResponse(RestResponseInterface $restResponse): RestResponseInterface;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addCouldNotUpdateCartError(RestResponseInterface $restResponse): RestResponseInterface;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addCartNotFoundError(RestResponseInterface $restResponse): RestResponseInterface;

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface $restResponse
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function addRequiredParameterIsMissingError(RestResponseInterface $restResponse): RestResponseInterface;
}
