<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartItemsResourceMapper as SprykerCartItemsResourceMapper;

class CartItemsResourceMapper extends SprykerCartItemsResourceMapper implements CartItemsResourceMapperInterface
{
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
