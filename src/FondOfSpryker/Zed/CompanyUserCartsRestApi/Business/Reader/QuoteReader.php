<?php

namespace FondOfSpryker\Zed\CompanyUserCartsRestApi\Business\Reader;

use FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;

class QuoteReader implements QuoteReaderInterface
{
    /**
     * @var \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface
     */
    protected $quoteFacade;

    /**
     * @param \FondOfSpryker\Zed\CompanyUserCartsRestApi\Dependency\Facade\CompanyUserCartsRestApiToQuoteFacadeInterface $quoteFacade
     */
    public function __construct(CompanyUserCartsRestApiToQuoteFacadeInterface $quoteFacade)
    {
        $this->quoteFacade = $quoteFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer|null
     */
    public function getByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): ?QuoteTransfer {
        $uuid = $restCompanyUserCartsRequestTransfer->getIdCart();

        if ($uuid === null) {
            return null;
        }

        $quoteTransfer = (new QuoteTransfer())
            ->setUuid($uuid);

        $quoteResponseTransfer = $this->quoteFacade->findQuoteByUuid($quoteTransfer);

        $quoteTransfer = $quoteResponseTransfer->getQuoteTransfer();

        if (
            $quoteTransfer === null || !$quoteResponseTransfer->getIsSuccessful()
            || !$this->isOwned($restCompanyUserCartsRequestTransfer, $quoteTransfer)
        ) {
            return null;
        }

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return bool
     */
    protected function isOwned(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer,
        QuoteTransfer $quoteTransfer
    ): bool {
        $companyUserReference = $restCompanyUserCartsRequestTransfer->getCompanyUserReference();
        $customerReference = $restCompanyUserCartsRequestTransfer->getCustomerReference();

        return $customerReference !== null
            && $companyUserReference !== null
            && $quoteTransfer->getCustomerReference() === $customerReference
            && $quoteTransfer->getCompanyUserReference() === $companyUserReference;
    }
}
