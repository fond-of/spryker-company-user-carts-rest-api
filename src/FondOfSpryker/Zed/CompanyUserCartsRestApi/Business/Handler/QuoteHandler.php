<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Handler;

use ArrayObject;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface;
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
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface
     */
    protected $quoteReloader;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Categorizer\ItemsCategorizerInterface $itemsCategorizer
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Adder\ItemAdderInterface $itemAdder
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Updater\ItemUpdaterInterface $itemUpdater
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover\ItemRemoverInterface $itemRemover
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reloader\QuoteReloaderInterface $quoteReloader
     */
    public function __construct(
        ItemsCategorizerInterface $itemsCategorizer,
        ItemAdderInterface $itemAdder,
        ItemUpdaterInterface $itemUpdater,
        ItemRemoverInterface $itemRemover,
        QuoteReloaderInterface $quoteReloader
    ) {
        $this->itemsCategorizer = $itemsCategorizer;
        $this->itemAdder = $itemAdder;
        $this->itemUpdater = $itemUpdater;
        $this->itemRemover = $itemRemover;
        $this->quoteReloader = $quoteReloader;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return void
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

        $quoteErrorTransfers = array_merge(
            $this->itemAdder->addMultiple($quoteTransfer, $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_ADDABLE]),
            $this->itemUpdater->updateMultiple($quoteTransfer, $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_UPDATABLE]),
            $this->itemRemover->removeMultiple($quoteTransfer, $categorisedItemTransfers[ItemsCategorizerInterface::CATEGORY_REMOVABLE]),
        );

        $quoteResponseTransfer = $this->quoteReloader->reload($quoteTransfer);

        $quoteErrorTransfers = array_merge(
            $quoteResponseTransfer->getErrors()
                ->getArrayCopy(),
            $quoteErrorTransfers,
        );

        return (new RestCompanyUserCartsResponseTransfer())
            ->setQuote(count($quoteErrorTransfers) > 0 ? null : $quoteResponseTransfer->getQuoteTransfer())
            ->setErrors(new ArrayObject($quoteErrorTransfers))
            ->setIsSuccessful(count($quoteErrorTransfers) === 0);
    }
}
