<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use Generated\Shared\Transfer\QuoteTransfer;

class CartOperation implements CartOperationInterface
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface
     */
    protected $cartClient;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface
     */
    protected $quoteClient;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface
     */
    protected $cartItemsResourceMapper;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface $cartClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface $quoteClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface $cartItemsResourceMapper
     */
    public function __construct(
        CompanyUserCartsRestApiToCartClientInterface $cartClient,
        CompanyUserCartsRestApiToQuoteClientInterface $quoteClient,
        CartItemsResourceMapperInterface $cartItemsResourceMapper
    ) {
        $this->cartClient = $cartClient;
        $this->quoteClient = $quoteClient;
        $this->cartItemsResourceMapper = $cartItemsResourceMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    public function setQuoteTransfer(QuoteTransfer $quoteTransfer): CartOperationInterface
    {
        $this->quoteClient->setQuote($quoteTransfer);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer[]|\ArrayObject $restCartItemTransfers
     *
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    public function handleItems(ArrayObject $restCartItemTransfers): CartOperationInterface
    {
        $itemTransfersToDelete = [];
        $itemTransfersToPersist = [];

        foreach ($restCartItemTransfers as $restCartItemTransfer) {
            $itemTransfer = $this->cartItemsResourceMapper
                ->mapRestCartItemTransferToItemTransfer($restCartItemTransfer);

            if ($restCartItemTransfer->getQuantity() === 0) {
                $itemTransfersToDelete[] = $itemTransfer;
                continue;
            }

            $itemTransfersToPersist[] = $itemTransfer;
        }

        $this->cartClient->addItems($itemTransfersToPersist);
        $this->cartClient->removeItems($itemTransfersToDelete);

        return $this;
    }

    /**
     * @return \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart\CartOperationInterface
     */
    public function reloadItems(): CartOperationInterface
    {
        $this->cartClient->reloadItems();

        return $this;
    }
}
