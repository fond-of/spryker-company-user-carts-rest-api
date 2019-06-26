<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

use FondOfSpryker\Client\CompanyUserQuote\CompanyUserQuoteClientInterface;
use Generated\Shared\Transfer\QuoteCollectionTransfer;
use Generated\Shared\Transfer\QuoteCriteriaFilterTransfer;

class CompanyUserCartsRestApiToCompanyUserQuoteClientBridge implements CompanyUserCartsRestApiToCompanyUserQuoteClientInterface
{
    /**
     * @var \FondOfSpryker\Client\CompanyUserQuote\CompanyUserQuoteClientInterface
     */
    protected $companyUserQuoteClient;

    /**
     * @param \FondOfSpryker\Client\CompanyUserQuote\CompanyUserQuoteClientInterface $companyUserQuoteClient
     */
    public function __construct(CompanyUserQuoteClientInterface $companyUserQuoteClient)
    {
        $this->companyUserQuoteClient = $companyUserQuoteClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteCollectionTransfer
     */
    public function getCompanyUserQuoteCollectionByCriteria(
        QuoteCriteriaFilterTransfer $quoteCriteriaFilterTransfer
    ): QuoteCollectionTransfer {
        return $this->companyUserQuoteClient->getCompanyUserQuoteCollectionByCriteria($quoteCriteriaFilterTransfer);
    }
}
