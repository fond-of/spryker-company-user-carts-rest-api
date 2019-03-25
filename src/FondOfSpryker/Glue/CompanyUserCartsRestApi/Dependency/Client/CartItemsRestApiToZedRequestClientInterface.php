<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Dependency\Client;

interface CartItemsRestApiToZedRequestClientInterface
{
    /**
     * @return \Generated\Shared\Transfer\MessageTransfer[]
     */
    public function getLastResponseErrorMessages();
}
