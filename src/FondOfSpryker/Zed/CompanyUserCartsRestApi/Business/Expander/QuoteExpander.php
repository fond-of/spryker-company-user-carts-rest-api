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
        $quoteTransfer = $this->expandWithCustomer($quoteTransfer, $restCompanyUserCartsRequestTransfer);

        return $this->expandWithCompanyUser($quoteTransfer, $restCompanyUserCartsRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function expandWithCustomer(
        QuoteTransfer $quoteTransfer,
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): QuoteTransfer {
        if ($quoteTransfer->getCustomer() !== null) {
            return $quoteTransfer;
        }

        $customerReference = $quoteTransfer->getCustomerReference();

        if ($customerReference === null) {
            $customerReference = $restCompanyUserCartsRequestTransfer->getCustomerReference();
        }

        return $quoteTransfer->setCustomer(
            (new CustomerTransfer())
                ->setCustomerReference($customerReference),
        );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function expandWithCompanyUser(
        QuoteTransfer $quoteTransfer,
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): QuoteTransfer {
        if ($quoteTransfer->getCompanyUser() !== null) {
            return $quoteTransfer;
        }

        $companyUserReference = $quoteTransfer->getCompanyUserReference();

        if ($companyUserReference === null) {
            $companyUserReference = $restCompanyUserCartsRequestTransfer->getCompanyUserReference();
        }

        return $quoteTransfer->setCompanyUser(
            (new CompanyUserTransfer())
                ->setCompanyUserReference($companyUserReference),
        );
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
