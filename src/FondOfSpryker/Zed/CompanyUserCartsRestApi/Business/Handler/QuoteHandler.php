<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\ItemUpdaterInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

class QuoteHandler implements QuoteHandlerInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface
     */
    protected $itemsCategorizer;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface
     */
    protected $itemAdder;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\ItemUpdaterInterface
     */
    protected $itemUpdater;

    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface
     */
    protected $itemRemover;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface $itemsCategorizer
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface $itemAdder
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\ItemUpdaterInterface $itemUpdater
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface $itemRemover
     */
    public function __construct(
        ItemsCategorizerInterface $itemsCategorizer,
        ItemAdderInterface $itemAdder,
        ItemUpdaterInterface $itemUpdater,
        ItemRemoverInterface $itemRemover
    ) {
        $this->itemsCategorizer = $itemsCategorizer;
        $this->itemAdder = $itemAdder;
        $this->itemUpdater = $itemUpdater;
        $this->itemRemover = $itemRemover;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function handle(
        QuoteTransfer $quoteTransfer,
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        $restCartsRequestAttributesTransfer = $restCompanyUserCartsRequestTransfer->getCart();

        if ($restCartsRequestAttributesTransfer === null) {
            return (new RestCompanyUserCartsResponseTransfer())
                ->setQuote($quoteTransfer)
                ->setIsSuccessful(true);
        }

        $categorisedItemTransfers = $this->itemsCategorizer->categorize(
            $quoteTransfer,
            $restCartsRequestAttributesTransfer,
        );

        $quoteResponseTransfer = $this->itemAdder->addMultiple(
            $quoteTransfer,
            $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_ADDABLE],
        );

        if (!$quoteResponseTransfer->getIsSuccessful() || $quoteResponseTransfer->getQuoteTransfer() === null) {
            return (new RestCompanyUserCartsResponseTransfer())
                ->setErrors($quoteResponseTransfer->getErrors())
                ->setIsSuccessful(false);
        }

        $quoteResponseTransfer = $this->itemUpdater->updateMultiple(
            $quoteResponseTransfer->getQuoteTransfer(),
            $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_UPDATABLE],
        );

        if (!$quoteResponseTransfer->getIsSuccessful() || $quoteResponseTransfer->getQuoteTransfer() === null) {
            return (new RestCompanyUserCartsResponseTransfer())
                ->setErrors($quoteResponseTransfer->getErrors())
                ->setIsSuccessful(false);
        }

        $quoteResponseTransfer = $this->itemRemover->removeMultiple(
            $quoteResponseTransfer->getQuoteTransfer(),
            $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_REMOVABLE],
        );

        return (new RestCompanyUserCartsResponseTransfer())
            ->setQuote($quoteResponseTransfer->getQuoteTransfer())
            ->setErrors($quoteResponseTransfer->getErrors())
            ->setIsSuccessful($quoteResponseTransfer->getIsSuccessful());
    }
}
