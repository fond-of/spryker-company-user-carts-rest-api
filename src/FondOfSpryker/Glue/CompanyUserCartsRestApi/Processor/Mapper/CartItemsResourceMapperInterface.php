<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer;

interface CartItemsResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function mapCartItemsRequestAttributesToItems(
        RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
    ): array;
}
