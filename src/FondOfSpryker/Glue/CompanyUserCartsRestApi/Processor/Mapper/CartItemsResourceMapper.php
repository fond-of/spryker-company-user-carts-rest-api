<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer;

class CartItemsResourceMapper implements CartItemsResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function mapCartItemsRequestAttributesToItems(
        RestCartItemsRequestAttributesTransfer $restCartItemsRequestAttributesTransfer
    ): array {
        $itemTransferList = [];
        $restCartItemTransferList = $restCartItemsRequestAttributesTransfer->getItems();

        foreach ($restCartItemTransferList as $restCartItemTransfer) {
            $itemTransfer = new ItemTransfer();
            $itemTransfer->fromArray($restCartItemTransfer->toArray(), true);
            $itemTransferList[] = $itemTransfer;
        }

        return $itemTransferList;
    }
}
