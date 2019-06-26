<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface as SprykerCartItemsResourceMapperInterface;

interface CartItemsResourceMapperInterface extends SprykerCartItemsResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    public function mapRestCartItemTransferToItemTransfer(
        RestCartItemTransfer $restCartItemTransfer
    ): ItemTransfer;
}
