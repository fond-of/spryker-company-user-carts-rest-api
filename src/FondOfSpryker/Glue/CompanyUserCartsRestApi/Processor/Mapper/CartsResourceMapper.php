<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartsResourceMapper as SprykerCartsResourceMapper;

class CartsResourceMapper extends SprykerCartsResourceMapper implements CartsResourceMapperInterface
{
    /**
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function mapRestCartsAttributesTransferToQuoteTransfer(
        RestCartsAttributesTransfer $restCartsAttributesTransfer
    ): QuoteTransfer {
        $quoteTransfer = new QuoteTransfer();

        $currencyTransfer = (new CurrencyTransfer())->setCode($restCartsAttributesTransfer->getCurrency());
        $storeTransfer = (new StoreTransfer())->setName($restCartsAttributesTransfer->getStore());

        return $quoteTransfer
            ->fromArray($restCartsAttributesTransfer->toArray(), true)
            ->setCurrency($currencyTransfer)
            ->setPriceMode($restCartsAttributesTransfer->getPriceMode())
            ->setStore($storeTransfer)
            ->setItems(new ArrayObject());
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function mapRestCartsRequestAttributesTransferToQuoteTransfer(
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): QuoteTransfer {
        $quoteTransfer = new QuoteTransfer();

        $currencyTransfer = (new CurrencyTransfer())->setCode($restCartsRequestAttributesTransfer->getCurrency());
        $storeTransfer = (new StoreTransfer())->setName($restCartsRequestAttributesTransfer->getStore());

        return $quoteTransfer
            ->fromArray($restCartsRequestAttributesTransfer->toArray(), true)
            ->setCurrency($currencyTransfer)
            ->setPriceMode($restCartsRequestAttributesTransfer->getPriceMode())
            ->setStore($storeTransfer)
            ->setItems(new ArrayObject());
    }
}
