<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use ArrayObject;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\Cart\CartClientInterface;

class CompanyUserCartsRestApiToCartClientBridge implements CompanyUserCartsRestApiToCartClientInterface
{
    /**
     * @var \Spryker\Client\Cart\CartClientInterface
     */
    protected $cartClient;

    /**
     * @param \Spryker\Client\Cart\CartClientInterface $cartClient
     */
    public function __construct(CartClientInterface $cartClient)
    {
        $this->cartClient = $cartClient;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     * @param array $params
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addItems(array $itemTransfers, array $params = []): QuoteTransfer
    {
        return $this->cartClient->addItems($itemTransfers, $params);
    }

    /**
     * @return void
     */
    public function reloadItems(): void
    {
        $this->cartClient->reloadItems();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function removeItems(array $itemTransfers): QuoteTransfer
    {
        return $this->cartClient->removeItems(new ArrayObject($itemTransfers));
    }
}
