<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Remover;

use ArrayObject;
use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToPersistentCartFacadeInterface;
use Generated\Shared\Transfer\PersistentCartChangeTransfer;
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
     * @return array<\Generated\Shared\Transfer\QuoteErrorTransfer>
     */
    public function removeMultiple(QuoteTransfer $quoteTransfer, array $itemTransfers): array
    {
        if (count($itemTransfers) === 0) {
            return [];
        }

        $persistentCartChangeTransfer = (new PersistentCartChangeTransfer())
            ->setCustomer($quoteTransfer->getCustomer())
            ->setIdQuote($quoteTransfer->getIdQuote())
            ->setItems(new ArrayObject($itemTransfers));

        $quoteResponseTransfer = $this->persistentCartFacade->remove($persistentCartChangeTransfer);

        return $quoteResponseTransfer->getErrors()
            ->getArrayCopy();
    }
}
