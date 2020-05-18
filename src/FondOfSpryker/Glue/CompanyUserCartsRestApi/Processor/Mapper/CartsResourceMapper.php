<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper;

use ArrayObject;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Generated\Shared\Transfer\RestCartsDiscountsTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Generated\Shared\Transfer\RestCartsTotalsTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use function in_array;

class CartsResourceMapper implements CartsResourceMapperInterface
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface
     */
    protected $cartItemsResourceMapper;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig
     */
    protected $config;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface $cartItemsResourceMapper
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig $config
     */
    public function __construct(
        CartItemsResourceMapperInterface $cartItemsResourceMapper,
        RestResourceBuilderInterface $restResourceBuilder,
        CompanyUserCartsRestApiConfig $config
    ) {
        $this->config = $config;
        $this->cartItemsResourceMapper = $cartItemsResourceMapper;
        $this->restResourceBuilder = $restResourceBuilder;
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

        $cartResource->setPayload($quoteTransfer);
        $this->mapCartItems($quoteTransfer, $cartResource, $restRequest->getMetadata()->getLocale());

        return $cartResource;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface $cartResource
     * @param string $localeName
     *
     * @return void
     */
    protected function mapCartItems(
        QuoteTransfer $quoteTransfer,
        RestResourceInterface $cartResource,
        string $localeName
    ): void {
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $itemResource = $this->restResourceBuilder->createRestResource(
                CartsRestApiConfig::RESOURCE_CART_ITEMS,
                $itemTransfer->getGroupKey(),
                $this->cartItemsResourceMapper->mapCartItemAttributes($itemTransfer, $localeName)
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
        $allowedFieldsToPatchInQuote = $this->config->getAllowedFieldsToPatchInQuote();

        foreach ($restAttributes as $restAttributeKey => $restAttributeValue) {
            if (!in_array($restAttributeKey, $allowedFieldsToPatchInQuote, true)) {
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

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return void
     */
    protected function setDiscounts(QuoteTransfer $quoteTransfer, RestCartsAttributesTransfer $restCartsAttributesTransfer): void
    {
        foreach ($quoteTransfer->getVoucherDiscounts() as $discountTransfer) {
            $restCartsDiscounts = new RestCartsDiscountsTransfer();
            $restCartsDiscounts->fromArray($discountTransfer->toArray(), true);
            $restCartsAttributesTransfer->addDiscount($restCartsDiscounts);
        }

        foreach ($quoteTransfer->getCartRuleDiscounts() as $discountTransfer) {
            $restCartsDiscounts = new RestCartsDiscountsTransfer();
            $restCartsDiscounts->fromArray($discountTransfer->toArray(), true);
            $restCartsAttributesTransfer->addDiscount($restCartsDiscounts);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return void
     */
    protected function setTotals(QuoteTransfer $quoteTransfer, RestCartsAttributesTransfer $restCartsAttributesTransfer): void
    {
        if ($quoteTransfer->getTotals() === null) {
            $restCartsAttributesTransfer->setTotals(new RestCartsTotalsTransfer());

            return;
        }

        $cartsTotalsTransfer = (new RestCartsTotalsTransfer())
            ->fromArray($quoteTransfer->getTotals()->toArray(), true);

        $taxTotalTransfer = $quoteTransfer->getTotals()->getTaxTotal();

        if ($taxTotalTransfer !== null) {
            $cartsTotalsTransfer->setTaxTotal($taxTotalTransfer->getAmount());
        }

        $restCartsAttributesTransfer->setTotals($cartsTotalsTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCartsAttributesTransfer $restCartsAttributesTransfer
     *
     * @return void
     */
    protected function setBaseCartData(
        QuoteTransfer $quoteTransfer,
        RestCartsAttributesTransfer $restCartsAttributesTransfer
    ): void {
        $restCartsAttributesTransfer->fromArray($quoteTransfer->toArray(), true);

        $restCartsAttributesTransfer
            ->setCurrency($quoteTransfer->getCurrency()->getCode())
            ->setStore($quoteTransfer->getStore()->getName());
    }
}
