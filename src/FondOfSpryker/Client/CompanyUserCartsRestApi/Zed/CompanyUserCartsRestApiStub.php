<?php

namespace FondOfSpryker\Client\CompanyUserCartsRestApi\Zed;

use FondOfSpryker\Client\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToZedRequestClientInterface;
use Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer;
use Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer;

class CompanyUserCartsRestApiStub implements CompanyUserCartsRestApiStubInterface
{
    /**
     * @var \FondOfSpryker\Client\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \FondOfSpryker\Client\CompanyUserCartsRestApi\Dependency\Client\CompanyUserCartsRestApiToZedRequestClientInterface $zedRequestClient
     */
    public function __construct(CompanyUserCartsRestApiToZedRequestClientInterface $zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    /**
     * @param \Generated\Shared\Transfer\RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer
     */
    public function updateQuoteByRestCompanyUserCartsRequest(
        RestCompanyUserCartsRequestTransfer $restCompanyUserCartsRequestTransfer
    ): RestCompanyUserCartsResponseTransfer {
        /** @var \Generated\Shared\Transfer\RestCompanyUserCartsResponseTransfer $restCompanyUserCartsResponseTransfer */
        $restCompanyUserCartsResponseTransfer = $this->zedRequestClient->call(
            '/company-user-carts-rest-api/gateway/update-quote-by-rest-company-user-carts-request',
            $restCompanyUserCartsRequestTransfer,
        );

        return $restCompanyUserCartsResponseTransfer;
    }
}
