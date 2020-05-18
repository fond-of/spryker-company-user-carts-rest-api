<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Generated\Shared\Transfer\RestItemsAttributesTransfer;

interface CartItemsResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param string $localeName
     *
     * @return \Generated\Shared\Transfer\RestItemsAttributesTransfer
     */
    public function mapCartItemAttributes(ItemTransfer $itemTransfer, string $localeName): RestItemsAttributesTransfer;

    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    public function mapRestCartItemTransferToItemTransfer(
        RestCartItemTransfer $restCartItemTransfer
    ): ItemTransfer;
}
