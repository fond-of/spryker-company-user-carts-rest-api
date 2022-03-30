<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PersistentCartChangeQuantityTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class ItemUpdater implements ItemUpdaterInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface
     */
    protected $persistentCartFacade;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
     */
    public function __construct(
        CompanyUserCartsRestApiToPersistentCartFacadeInterface $persistentCartFacade
    ) {
        $this->persistentCartFacade = $persistentCartFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return array<\Generated\Shared\Transfer\QuoteErrorTransfer>
     */
    public function update(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer): array
    {
        $persistentCartChangeQuantityTransfer = (new PersistentCartChangeQuantityTransfer())
            ->setCustomer($quoteTransfer->getCustomer())
            ->setIdQuote($quoteTransfer->getIdQuote())
            ->setItem($itemTransfer);

        $quoteResponseTransfer = $this->persistentCartFacade->changeItemQuantity($persistentCartChangeQuantityTransfer);

        return $quoteResponseTransfer->getErrors()
            ->getArrayCopy();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return array<\Generated\Shared\Transfer\QuoteErrorTransfer>
     */
    public function updateMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): array
    {
        $mergedQuoteErrorTransfers = [];

        foreach ($itemTransfers as $itemTransfer) {
            $quoteErrorTransfers = $this->update($quoteTransfer, $itemTransfer);

            if (count($quoteErrorTransfers) > 0) {
                $mergedQuoteErrorTransfers = array_merge($quoteErrorTransfers, $mergedQuoteErrorTransfers);
            }
        }

        return $mergedQuoteErrorTransfers;
    }
}
