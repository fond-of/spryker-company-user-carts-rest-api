<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor;

use Codeception\Test\Unit;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use Generated\Shared\Transfer\QuoteTransfer;

class CompanyUserCartsRestApiConfigTest extends Unit
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig
     */
    protected $companyUserCartsRestApiConfig;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->companyUserCartsRestApiConfig = new CompanyUserCartsRestApiConfig();
    }

    /**
     * @return void
     */
    public function testGetAllowedFieldsToPatchInQuote(): void
    {
        $this->assertSame([
            QuoteTransfer::NAME,
            QuoteTransfer::COMMENT,
            QuoteTransfer::FILTERS,
            QuoteTransfer::REFERENCE,
        ], $this->companyUserCartsRestApiConfig->getAllowedFieldsToPatchInQuote());
    }
}
