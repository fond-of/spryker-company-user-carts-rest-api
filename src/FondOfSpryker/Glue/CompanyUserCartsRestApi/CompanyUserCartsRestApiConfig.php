<?php

declare(strict_types = 1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use Spryker\Glue\Kernel\AbstractBundleConfig;

class CompanyUserCartsRestApiConfig extends AbstractBundleConfig
{
    public const RESOURCE_CARTS = 'carts';

    public const CONTROLLER_CARTS = 'carts-resource';

    public const RESPONSE_CODE_REQUIRED_PARAMETER_IS_MISSING = '2000';
    public const RESPONSE_CODE_CART_NOT_FOUND = '2001';

    public const RESPONSE_MESSAGE_REQUIRED_PARAMETER_IS_MISSING = 'Required parameter is missing.';
    public const RESPONSE_MESSAGE_CART_NOT_FOUND = 'Cart not found.';
}
