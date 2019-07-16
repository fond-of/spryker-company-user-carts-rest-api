<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartsResourceMapper as SprykerCartsResourceMapper;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;

use function in_array;

class CartsResourceMapper extends SprykerCartsResourceMapper implements CartsResourceMapperInterface
{
    /**
     * @var string[]
     */
    protected $allowedFieldsToPatch;

    /**
     * @param \Spryker\Glue\CartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface $cartItemsResourceMapper
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param string[] $allowedFieldsToUpdate
     */
    public function __construct(
        CartItemsResourceMapperInterface $cartItemsResourceMapper,
        RestResourceBuilderInterface $restResourceBuilder,
        array $allowedFieldsToUpdate
    ) {
        parent::__construct($cartItemsResourceMapper, $restResourceBuilder);
        $this->allowedFieldsToPatch = $allowedFieldsToUpdate;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function mapMinimalRestCartsRequestAttributesTransferToQuoteTransfer(
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer,
        QuoteTransfer $quoteTransfer
    ): QuoteTransfer {

        $quoteAttributes = $quoteTransfer->toArray();
        $restAttributes = $restCartsRequestAttributesTransfer->toArray();

        foreach ($restAttributes as $restAttributeKey => $restAttributeValue) {
            if (!in_array($restAttributeKey, $this->allowedFieldsToPatch, true)) {
                continue;
            }

            $quoteAttributes[$restAttributeKey] = $restAttributeValue;
        }

        return $quoteTransfer->fromArray($quoteAttributes, true);
    }

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
