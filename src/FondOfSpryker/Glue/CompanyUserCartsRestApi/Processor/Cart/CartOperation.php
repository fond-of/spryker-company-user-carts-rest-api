<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use ArrayObject;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartItemTransfer;

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
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface[]
     */
    protected $restCartItemExpanderPlugins;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToCartClientInterface $cartClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToQuoteClientInterface $quoteClient
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\CartItemsResourceMapperInterface $cartItemsResourceMapper
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Plugin\RestCartItemExpanderPluginInterface[] $restCartItemExpanderPlugins
     */
    public function __construct(
        CompanyUserCartsRestApiToCartClientInterface $cartClient,
        CompanyUserCartsRestApiToQuoteClientInterface $quoteClient,
        CartItemsResourceMapperInterface $cartItemsResourceMapper,
        array $restCartItemExpanderPlugins
    ) {
        $this->cartClient = $cartClient;
        $this->quoteClient = $quoteClient;
        $this->cartItemsResourceMapper = $cartItemsResourceMapper;
        $this->restCartItemExpanderPlugins = $restCartItemExpanderPlugins;
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
        $itemTransfersToAdd = [];
        $itemTransfersToRemove = [];
        $itemTransfersToUpdate = [];

        foreach ($restCartItemTransfers as $restCartItemTransfer) {
            $this->executeExpandRestCartItemPlugins($restCartItemTransfer);

            $itemTransfer = $this->cartItemsResourceMapper
                ->mapRestCartItemTransferToItemTransfer($restCartItemTransfer);

            $existingItemTransfer = $this->findItemInQuote($itemTransfer);

            if ($this->canAddItem($itemTransfer, $existingItemTransfer)) {
                $itemTransfersToAdd[] = $itemTransfer;
                continue;
            }

            if ($this->canRemoveItem($itemTransfer, $existingItemTransfer)) {
                $itemTransfersToRemove[] = $itemTransfer;
                continue;
            }

            if ($this->canUpdateItem($itemTransfer, $existingItemTransfer)) {
                $itemTransfersToUpdate[] = $itemTransfer;
                continue;
            }
        }

        $this->addItems($itemTransfersToAdd)
            ->removeItems($itemTransfersToRemove)
            ->updateItems($itemTransfersToUpdate);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer|null $existingItemTransfer
     *
     * @return bool
     */
    protected function canAddItem(
        ItemTransfer $itemTransfer,
        ?ItemTransfer $existingItemTransfer
    ): bool {
        return $itemTransfer->getQuantity() !== 0 && $existingItemTransfer === null;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer|null $existingItemTransfer
     *
     * @return bool
     */
    protected function canRemoveItem(
        ItemTransfer $itemTransfer,
        ?ItemTransfer $existingItemTransfer
    ): bool {
        return $itemTransfer->getQuantity() === 0 && $existingItemTransfer !== null;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer|null $existingItemTransfer
     *
     * @return bool
     */
    protected function canUpdateItem(
        ItemTransfer $itemTransfer,
        ?ItemTransfer $existingItemTransfer
    ): bool {
        return $itemTransfer->getQuantity() !== 0 && $existingItemTransfer !== null
            && $existingItemTransfer->getQuantity() - $itemTransfer->getQuantity() !== 0;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfersToPersist
     *
     * @return $this
     */
    protected function addItems(array $itemTransfersToPersist)
    {
        if (count($itemTransfersToPersist) === 0) {
            return $this;
        }

        $this->cartClient->addItems($itemTransfersToPersist);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfersToUpdate
     *
     * @return $this
     */
    protected function updateItems(array $itemTransfersToUpdate)
    {
        foreach ($itemTransfersToUpdate as $itemTransfer) {
            $this->cartClient->changeItemQuantity(
                $itemTransfer->getSku(),
                $itemTransfer->getGroupKey(),
                $itemTransfer->getQuantity()
            );
        }

        return $this;
    }

    /**
     * @param array $itemTransfersToRemove
     *
     * @return $this
     */
    protected function removeItems(array $itemTransfersToRemove)
    {
        if (count($itemTransfersToRemove) === 0) {
            return $this;
        }

        $this->cartClient->removeItems($itemTransfersToRemove);

        return $this;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    protected function findItemInQuote(ItemTransfer $itemTransfer): ?ItemTransfer
    {
        $quoteTransfer = $this->quoteClient->getQuote();

        return $this->cartClient->findQuoteItem($quoteTransfer, $itemTransfer->getSku(), $itemTransfer->getGroupKey());
    }

    /**
     * @param \Generated\Shared\Transfer\RestCartItemTransfer $restCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\RestCartItemTransfer
     */
    protected function executeExpandRestCartItemPlugins(RestCartItemTransfer $restCartItemTransfer): RestCartItemTransfer
    {
        foreach ($this->restCartItemExpanderPlugins as $restCartItemExpanderPlugin) {
            $restCartItemTransfer = $restCartItemExpanderPlugin->expand($restCartItemTransfer);
        }

        return $restCartItemTransfer;
    }
}
