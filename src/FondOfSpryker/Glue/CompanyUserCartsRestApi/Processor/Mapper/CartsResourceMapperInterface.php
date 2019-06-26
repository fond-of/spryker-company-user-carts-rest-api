<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartsResourceMapperInterface as SprykerCartsResourceMapperInterface;

interface CartsResourceMapperInterface extends SprykerCartsResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function mapRestCartsAttributesTransferToQuoteTransfer(
        RestCartsAttributesTransfer $restCartsAttributesTransfer
    ): QuoteTransfer;

    /**
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function mapRestCartsRequestAttributesTransferToQuoteTransfer(
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): QuoteTransfer;
}
