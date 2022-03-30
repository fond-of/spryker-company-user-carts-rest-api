<?php

namespace FondOfSpryker\Client\CompanyUserCartsRestApi;

use FondOfSpryker\Client\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToZedRequestClientInterface;
use FondOfSpryker\Client\CompanyUserCartsRestApi\Zed\CompanyUserCartsRestApiStub;
use FondOfSpryker\Client\CompanyUserCartsRestApi\Zed\CompanyUserCartsRestApiStubInterface;
use Spryker\Client\Kernel\AbstractFactory;

class CompanyUserCartsRestApiFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Client\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToZedRequestClientInterface
     */
    protected function getZedRequestClient(): CompanyUserCartsRestApiToZedRequestClientInterface
    {
        return $this->getProvidedDependency(CompanyUserCartsRestApiDependencyProvider::CLIENT_ZED_REQUEST);
    }

    /**
     * @return \FondOfSpryker\Client\CompanyUserCartsRestApi\Zed\CompanyUserCartsRestApiStubInterface
     */
    public function createCompanyUserCartsRestApiStub(): CompanyUserCartsRestApiStubInterface
    {
        return new CompanyUserCartsRestApiStub(
            $this->getZedRequestClient(),
        );
    }
}
