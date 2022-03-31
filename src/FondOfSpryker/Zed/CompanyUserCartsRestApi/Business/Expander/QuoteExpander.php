<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Expander;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
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
        $quoteTransfer = $this->expandWithConfigurableFields($quoteTransfer, $restCompanyUserCartsRequestTransfer);
        $quoteTransfer = $this->expandWithCustomer($quoteTransfer);

        return $this->expandWithCompanyUser($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function expandWithCustomer(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getCustomer() !== null) {
            return $quoteTransfer;
        }

        $customerTransfer = (new CustomerTransfer())
            ->setCustomerReference($quoteTransfer->getCustomerReference());

        return $quoteTransfer->setCustomer($customerTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function expandWithCompanyUser(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        if ($quoteTransfer->getCompanyUser() !== null) {
            return $quoteTransfer;
        }

        $companyUserTransfer = (new CompanyUserTransfer())
            ->setCompanyUserReference($quoteTransfer->getCompanyUserReference());

        return $quoteTransfer->setCompanyUser($companyUserTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function expandWithConfigurableFields(
        QuoteTransfer $quoteTransfer,
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): QuoteTransfer {
        $restCartsRequestAttributesTransfer = $restCompanyUserCartsRequestTransfer->getCart();

        if ($restCartsRequestAttributesTransfer === null) {
            return $quoteTransfer;
        }

        $allowedFieldsToPatchInQuote = $this->config->getAllowedFieldsToPatchInQuote();

        foreach ($restCartsRequestAttributesTransfer->modifiedToArray() as $key => $value) {
            $method = sprintf('set%s', ucfirst(str_replace('_', '', ucwords($key, '_'))));

            if (!in_array($key, $allowedFieldsToPatchInQuote, true) || !method_exists($quoteTransfer, $method)) {
                continue;
            }

            $quoteTransfer->$method($value);
        }

        return $quoteTransfer;
    }
}
