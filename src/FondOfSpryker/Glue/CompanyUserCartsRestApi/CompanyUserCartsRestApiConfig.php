<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi;

use Spryker\Glue\Kernel\AbstractBundleConfig;

class CompanyUserCartsRestApiConfig extends AbstractBundleConfig
{
    public const ACTION_CART_ITEMS_POST = 'post';
    public const RESOURCE_CART_ITEMS = 'items';
    public const CONTROLLER_CART_ITEMS = 'cart-items-resource';
}
