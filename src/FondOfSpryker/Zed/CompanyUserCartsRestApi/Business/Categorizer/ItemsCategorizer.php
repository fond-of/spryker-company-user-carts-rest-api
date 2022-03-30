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
     * @return array<string, array<string, \Generated\Shared\Transfer\ItemTransfer>>
     */
    public function categorize(
        QuoteTransfer $quoteTransfer,
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): array {
        $categorisedItemTransfers = [
            static::CATEGORY_ADDABLE => [],
            static::CATEGORY_UPDATABLE => [],
            static::CATEGORY_REMOVABLE => [],
        ];

        foreach ($restCartsRequestAttributesTransfer->getItems() as $restCartItemTransfer) {
            $oldItemTransfer = $this->itemFinder->findInQuoteByRestCartItem($quoteTransfer, $restCartItemTransfer);
            $itemTransfer = $this->itemMapper->fromRestCartItem($restCartItemTransfer);

            if ($oldItemTransfer === null) {
                $categorisedItemTransfers[static::CATEGORY_ADDABLE][] = $itemTransfer;

                continue;
            }

            if ($restCartItemTransfer->getQuantity() === 0) {
                $categorisedItemTransfers[static::CATEGORY_REMOVABLE][] = $itemTransfer;

                continue;
            }

            if ($restCartItemTransfer->getQuantity() !== $itemTransfer->getQuantity()) {
                $categorisedItemTransfers[static::CATEGORY_UPDATABLE][] = $itemTransfer;
            }
        }

        return $categorisedItemTransfers;
    }
}
