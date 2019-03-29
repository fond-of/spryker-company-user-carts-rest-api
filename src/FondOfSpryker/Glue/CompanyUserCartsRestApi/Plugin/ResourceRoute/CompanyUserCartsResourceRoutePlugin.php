<?php

declare(strict_types=1);

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Plugin\ResourceRoute;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use FondOfSpryker\Glue\CompanyUsersRestApi\CompanyUsersRestApiConfig;
use Generated\Shared\Transfer\RestCartsAttributesTransfer;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRouteCollectionInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRoutePluginInterface;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceWithParentPluginInterface;
use Spryker\Glue\Kernel\AbstractPlugin;

/**
 * @method \FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiFactory getFactory()
 */
class CompanyUserCartsResourceRoutePlugin extends AbstractPlugin implements ResourceRoutePluginInterface, ResourceWithParentPluginInterface
{
    /**
     * @param \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRouteCollectionInterface $resourceRouteCollection
     *
     * @return \Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\ResourceRouteCollectionInterface
     */
    public function configure(ResourceRouteCollectionInterface $resourceRouteCollection): ResourceRouteCollectionInterface
    {
        $resourceRouteCollection
            ->addGet('get');

        return $resourceRouteCollection;
    }

    /**
     * @return string
     */
    public function getResourceType(): string
    {
        return CompanyUserCartsRestApiConfig::RESOURCE_CARTS;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return CompanyUserCartsRestApiConfig::CONTROLLER_CARTS;
    }

    /**
     * @return string
     */
    public function getResourceAttributesClassName(): string
    {
        return RestCartsAttributesTransfer::class;
    }

    /**
     * @return string
     */
    public function getParentResourceType(): string
    {
        return CompanyUsersRestApiConfig::RESOURCE_COMPANY_USERS;
    }
}