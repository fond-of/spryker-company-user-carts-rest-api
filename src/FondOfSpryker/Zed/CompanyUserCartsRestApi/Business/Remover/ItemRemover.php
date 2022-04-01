<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover;

use ArrayObject;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class ItemRemover implements ItemRemoverInterface
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
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function removeMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): QuoteResponseTransfer
    {
        if (count($itemTransfers) === 0) {
            return (new QuoteResponseTransfer())
                ->setQuoteTransfer($quoteTransfer)
                ->setIsSuccessful(true);
        }

        $persistentCartChangeTransfer = (new PersistentCartChangeTransfer())
            ->setCustomer($quoteTransfer->getCustomer())
            ->setIdQuote($quoteTransfer->getIdQuote())
            ->setItems(new ArrayObject($itemTransfers));

        return $this->persistentCartFacade->remove($persistentCartChangeTransfer);
    }
}
