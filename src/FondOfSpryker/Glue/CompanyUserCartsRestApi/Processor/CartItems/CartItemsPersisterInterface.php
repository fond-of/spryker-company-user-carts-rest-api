<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\CartItems;

use Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

interface CartItemsPersisterInterface
{
    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function persist(
        RestRequestInterface $restRequest,
        RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
    ): RestResponseInterface;
}
