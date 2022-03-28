<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Glue\Kernel\AbstractBundleConfig;

class CompanyUserCartsRestApiConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const RESOURCE_COMPANY_USER_CARTS = 'company-user-carts';

    /**
     * @var string
     */
    public const RESOURCE_COMPANY_USERS = 'company-user';

    /**
     * @var string
     */
    public const CONTROLLER_CARTS = 'carts-resource';

    /**
     * @var string
     */
    public const RESPONSE_CODE_REQUIRED_PARAMETER_IS_MISSING = '2000';

    /**
     * @var string
     */
    public const RESPONSE_CODE_CART_NOT_FOUND = '2001';

    /**
     * @var string
     */
    public const RESPONSE_CODE_COULD_NOT_UPDATE_CART = '2002';

    /**
     * @var string
     */
    public const RESPONSE_CODE_COMPANY_USER_NOT_FOUND = '2004';

    /**
     * @var string
     */
    public const RESPONSE_MESSAGE_REQUIRED_PARAMETER_IS_MISSING = 'Required parameter is missing.';

    /**
     * @var string
     */
    public const RESPONSE_MESSAGE_CART_NOT_FOUND = 'Cart not found.';

    /**
     * @var string
     */
    public const RESPONSE_MESSAGE_COULD_NOT_UPDATE_CART = 'Could not update cart properties.';

    /**
     * @var string
     */
    public const RESPONSE_MESSAGE_COMPANY_USER_NOT_FOUND = 'Company user not found.';

    /**
     * @var string
     */
    public const FORMAT_SELF_LINK_CART_RESOURCE = '%s/%s/%s/%s';

    /**
     * @var string
     */
    public const RESOURCE_CARTS = 'carts';

    /**
     * @var string
     */
    public const RESOURCE_CART_ITEMS = 'items';

    /**
     * @return array<string>
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
