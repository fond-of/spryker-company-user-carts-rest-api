<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\Kernel\AbstractBundleConfig;

class CompanyUserCartsRestApiConfig extends AbstractBundleConfig
{
    public const RESOURCE_COMPANY_USER_CARTS = 'company-user-carts';

    public const CONTROLLER_CARTS = 'carts-resource';

    public const RESPONSE_CODE_REQUIRED_PARAMETER_IS_MISSING = '2000';
    public const RESPONSE_CODE_CART_NOT_FOUND = '2001';
    public const RESPONSE_CODE_COULD_NOT_UPDATE_CART = '2002';

    public const RESPONSE_MESSAGE_REQUIRED_PARAMETER_IS_MISSING = 'Required parameter is missing.';
    public const RESPONSE_MESSAGE_CART_NOT_FOUND = 'Cart not found.';
    public const RESPONSE_MESSAGE_COULD_NOT_UPDATE_CART = 'Could not update cart properties.';

    public const FORMAT_SELF_LINK_CART_RESOURCE = '%s/%s/%s/%s';
    public const RESOURCE_CARTS = 'carts';
    public const RESOURCE_CART_ITEMS = 'items';

    /**
     * @return string[]
     */
    public function getAllowedFieldsToPatchInQuote(): array
    {
        return [
            QuoteTransfer::NAME,
            QuoteTransfer::COMMENT,
            QuoteTransfer::FILTERS,
            QuoteTransfer::REFERENCE,
        ];
    }
}
