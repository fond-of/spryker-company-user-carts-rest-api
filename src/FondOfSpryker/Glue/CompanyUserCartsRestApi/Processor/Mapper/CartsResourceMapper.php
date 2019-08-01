<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use ArrayObject;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use Spryker\Glue\CartsRestApi\Processor\Mapper\CartsResourceMapper as SprykerCartsResourceMapper;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
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
     * @param \Spryker\Glue\CartsRestApi\CartsRestApiConfig $config
     * @param string[] $allowedFieldsToUpdate
     */
    public function __construct(
        CartItemsResourceMapperInterface $cartItemsResourceMapper,
        RestResourceBuilderInterface $restResourceBuilder,
        CartsRestApiConfig $config,
        array $allowedFieldsToUpdate
    ) {
        parent::__construct(
            $cartItemsResourceMapper,
            $restResourceBuilder,
            $config
        );

        $this->allowedFieldsToPatch = $allowedFieldsToUpdate;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface
     */
    public function mapCartsResource(
        QuoteTransfer $quoteTransfer,
        RestRequestInterface $restRequest
    ): RestResourceInterface {
        $restCartsAttributesTransfer = new RestCartsAttributesTransfer();

        $this->setBaseCartData($quoteTransfer, $restCartsAttributesTransfer);
        $this->setTotals($quoteTransfer, $restCartsAttributesTransfer);
        $this->setDiscounts($quoteTransfer, $restCartsAttributesTransfer);

        $cartResource = $this->restResourceBuilder->createRestResource(
            CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USER_CARTS,
            $quoteTransfer->getUuid(),
            $restCartsAttributesTransfer
        );

        $this->mapCartItems($quoteTransfer, $cartResource);

        return $cartResource;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface $cartResource
     *
     * @return void
     */
    protected function mapCartItems(
        QuoteTransfer $quoteTransfer,
        RestResourceInterface $cartResource
    ): void {
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemResource = $this->restResourceBuilder->createRestResource(
                CartsRestApiConfig::RESOURCE_CART_ITEMS,
                $itemTransfer->getGroupKey(),
                $this->cartItemsResourceMapper->mapCartItemAttributes($itemTransfer)
            );

            $itemResource->addLink(
                RestLinkInterface::LINK_SELF,
                sprintf(
                    '%s/%s/%s/%s',
                    CompanyUserCartsRestApiConfig::RESOURCE_CARTS,
                    $cartResource->getId(),
                    CompanyUserCartsRestApiConfig::RESOURCE_CART_ITEMS,
                    $itemTransfer->getGroupKey()
                )
            );

            $cartResource->addRelationship($itemResource);
        }
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
        $restAttributes = $restCartsRequestAttributesTransfer->modifiedToArray();

        foreach ($restAttributes as $restAttributeKey => $restAttributeValue) {
            if (!in_array($restAttributeKey, $this->allowedFieldsToPatch, true)) {
                continue;
            }

            $quoteAttributes[$restAttributeKey] = $restAttributeValue;
        }

        return $quoteTransfer->fromArray($quoteAttributes, true);
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
