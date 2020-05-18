<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemCalculationsTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Generated\Shared\Transfer\RestItemsAttributesTransfer;

class CartItemsResourceMapper implements CartItemsResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\RestItemsAttributesTransfer
     */
    public function mapCartItemAttributes(ItemTransfer $itemTransfer, string $localeName): RestItemsAttributesTransfer
    {
        $itemData = $itemTransfer->toArray();

        $restCartItemsAttributesResponseTransfer = (new RestItemsAttributesTransfer())
            ->fromArray($itemData, true);

        $calculationsTransfer = (new RestCartItemCalculationsTransfer())->fromArray($itemData, true);
        $restCartItemsAttributesResponseTransfer->setCalculations($calculationsTransfer);

        return $restCartItemsAttributesResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    public function mapRestCartItemTransferToItemTransfer(RestCartItemTransfer $restCartItemTransfer): ItemTransfer
    {
        return (new ItemTransfer())->fromArray($restCartItemTransfer->toArray(), true);
    }
}
