<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapperInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;

class ItemsCategorizer implements ItemsCategorizerInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapperInterface
     */
    protected $itemMapper;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinderInterface
     */
    protected $itemFinder;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Mapper\ItemMapperInterface $itemMapper
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Finder\ItemFinderInterface $itemFinder
     */
    public function __construct(
        ItemMapperInterface $itemMapper,
        ItemFinderInterface $itemFinder
    ) {
        $this->itemMapper = $itemMapper;
        $this->itemFinder = $itemFinder;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     *
     * @return array<string, array<\Generated\Shared\Transfer\ItemTransfer>>
     */
    public function categorize(
        QuoteTransfer $quoteTransfer,
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): array {
        $categorisedItemTransfers = [
            static::CATEGORY_ADDABLE => [],
            static::CATEGORY_REMOVABLE => [],
        ];

        foreach ($restCartsRequestAttributesTransfer->getItems() as $restCartItemTransfer) {
            $oldItemTransfer = $this->itemFinder->findInQuoteByRestCartItem($quoteTransfer, $restCartItemTransfer);
            $newQuantity = $restCartItemTransfer->getQuantity();

            if ($oldItemTransfer === null && $newQuantity > 0) {
                $categorisedItemTransfers[static::CATEGORY_ADDABLE][] = $this->itemMapper->fromRestCartItem(
                    $restCartItemTransfer,
                );

                continue;
            }

            if ($oldItemTransfer !== null && $newQuantity === 0) {
                $categorisedItemTransfers[static::CATEGORY_REMOVABLE][] = $this->itemMapper->fromRestCartItem(
                    $restCartItemTransfer,
                );

                continue;
            }

            if ($oldItemTransfer !== null && $newQuantity - $oldItemTransfer->getQuantity() < 0) {
                $categorisedItemTransfers[static::CATEGORY_REMOVABLE][] = $this->itemMapper->fromRestCartItem(
                    $restCartItemTransfer,
                )->setQuantity(abs($newQuantity - $oldItemTransfer->getQuantity()));

                continue;
            }

            if ($oldItemTransfer !== null && $newQuantity - $oldItemTransfer->getQuantity() > 0) {
                $categorisedItemTransfers[static::CATEGORY_ADDABLE][] = $this->itemMapper->fromRestCartItem(
                    $restCartItemTransfer,
                )->setQuantity($newQuantity - $oldItemTransfer->getQuantity());
            }
        }

        return $categorisedItemTransfers;
    }
}
