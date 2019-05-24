<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Cart;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use FondOfSpryker\Glue\CompanyUsersRestApi\CompanyUsersRestApiConfig;
use Generated\Shared\Transfer\QuoteTransfer;

trait SelfLinkCreatorTrait
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function createSelfLink(QuoteTransfer $quoteTransfer): string
    {
        return sprintf(
            CompanyUserCartsRestApiConfig::FORMAT_SELF_LINK_CART_RESOURCE,
            CompanyUsersRestApiConfig::RESOURCE_COMPANY_USERS,
            $quoteTransfer->getCompanyUserReference(),
            CompanyUserCartsRestApiConfig::RESOURCE_CARTS,
            $quoteTransfer->getUuid()
        );
    }
}
