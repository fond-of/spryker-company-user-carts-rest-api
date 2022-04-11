<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PersistentCartChangeQuantityTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
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
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function update(QuoteTransfer $quoteTransfer, ItemTransfer $itemTransfer): QuoteResponseTransfer
    {
        $persistentCartChangeQuantityTransfer = (new PersistentCartChangeQuantityTransfer())
            ->setCustomer($quoteTransfer->getCustomer())
            ->setIdQuote($quoteTransfer->getIdQuote())
            ->setItem($itemTransfer);

        return $this->persistentCartFacade->changeItemQuantity($persistentCartChangeQuantityTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function updateMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): QuoteResponseTransfer
    {
        $quoteResponseTransfer = (new QuoteResponseTransfer())
            ->setQuoteTransfer($quoteTransfer)
            ->setIsSuccessful(true);

        foreach ($itemTransfers as $itemTransfer) {
            $quoteResponseTransfer = $this->update($quoteTransfer, $itemTransfer);

            if (!$quoteResponseTransfer->getIsSuccessful()) {
                return $quoteResponseTransfer;
            }
        }

        return $quoteResponseTransfer;
    }
}
