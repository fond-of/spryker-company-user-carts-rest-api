<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface RestResponseBuilderInterface
{
    /**
     * @param array<\Generated\Shared\Transfer\QuoteErrorTransfer> $quoteErrorTransfers
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function buildNotUpdatedRestResponse(array $quoteErrorTransfers): RestResponseInterface;

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function buildUpdatedRestResponse(QuoteTransfer $quoteTransfer): RestResponseInterface;
}
