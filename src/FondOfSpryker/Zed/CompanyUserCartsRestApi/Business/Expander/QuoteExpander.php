<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;

class QuoteExpander implements QuoteExpanderInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig
     */
    protected $config;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig $config
     */
    public function __construct(CompanyUserCartsRestApiConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function expand(
        QuoteTransfer $quoteTransfer,
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): QuoteTransfer {
        $restCartsRequestAttributesTransfer = $restCompanyUserCartsRequestTransfer->getCart();

        if ($restCartsRequestAttributesTransfer === null) {
            return $quoteTransfer;
        }

        $quoteData = $quoteTransfer->toArray();
        $allowedFieldsToPatchInQuote = $this->config->getAllowedFieldsToPatchInQuote();

        foreach ($restCartsRequestAttributesTransfer->modifiedToArray() as $key => $value) {
            if (!in_array($key, $allowedFieldsToPatchInQuote, true)) {
                continue;
            }

            $quoteData[$key] = $value;
        }

        return $quoteTransfer
            ->fromArray($quoteData, true);
    }
}
