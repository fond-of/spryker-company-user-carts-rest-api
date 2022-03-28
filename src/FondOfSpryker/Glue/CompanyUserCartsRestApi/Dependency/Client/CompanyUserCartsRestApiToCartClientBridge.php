<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer;
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
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     * @param array $params
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function addItems(array $itemTransfers, array $params = []): QuoteTransfer
    {
        return $this->cartClient->addItems($itemTransfers, $params);
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function removeItems(array $itemTransfers): QuoteTransfer
    {
        return $this->cartClient->removeItems(new ArrayObject($itemTransfers));
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $sku
     * @param string|null $groupKey
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    public function findQuoteItem(QuoteTransfer $quoteTransfer, string $sku, ?string $groupKey = null): ?ItemTransfer
    {
        return $this->cartClient->findQuoteItem($quoteTransfer, $sku, $groupKey);
    }

    /**
     * @param string $sku
     * @param string|null $groupKey
     * @param int $quantity
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function changeItemQuantity(string $sku, ?string $groupKey = null, int $quantity = 1): QuoteTransfer
    {
        return $this->cartClient->changeItemQuantity($sku, $groupKey, $quantity);
    }
}
