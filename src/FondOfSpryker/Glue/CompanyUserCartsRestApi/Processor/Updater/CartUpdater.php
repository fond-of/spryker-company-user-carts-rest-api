<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Updater;

use FondOfSpryker\Client\CompanyUserCartsRestApi\CompanyUserCartsRestApiClientInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpanderInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapperInterface;
use Generated\Shared\Transfer\RestCartsRequestAttributesTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CartUpdater implements CartUpdaterInterface
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapperInterface
     */
    protected $restCompanyUserCartsRequestMapper;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpanderInterface
     */
    protected $restCartItemExpander;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface
     */
    protected $restResponseBuilder;

    /**
     * @var \FondOfSpryker\Client\CompanyUserCartsRestApi\CompanyUserCartsRestApiClientInterface
     */
    protected $client;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCompanyUserCartsRequestMapperInterface $restCompanyUserCartsRequestMapper
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Expander\RestCartItemExpanderInterface $restCartItemExpander
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder\RestResponseBuilderInterface $restResponseBuilder
     * @param \FondOfSpryker\Client\CompanyUserCartsRestApi\CompanyUserCartsRestApiClientInterface $client
     */
    public function __construct(
        RestCompanyUserCartsRequestMapperInterface $restCompanyUserCartsRequestMapper,
        RestCartItemExpanderInterface $restCartItemExpander,
        RestResponseBuilderInterface $restResponseBuilder,
        CompanyUserCartsRestApiClientInterface $client
    ) {
        $this->restCompanyUserCartsRequestMapper = $restCompanyUserCartsRequestMapper;
        $this->restCartItemExpander = $restCartItemExpander;
        $this->restResponseBuilder = $restResponseBuilder;
        $this->client = $client;
    }

    /**
     * @param \Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface $restRequest
     * @param \Generated\Shared\Transfer\RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function update(
        RestRequestInterface $restRequest,
        RestCartsRequestAttributesTransfer $restCartsRequestAttributesTransfer
    ): RestResponseInterface {
        $restCartItemTransfers = $restCartsRequestAttributesTransfer->getItems();

        foreach ($restCartItemTransfers as $index => $restCartItemTransfer) {
            $restCartItemTransfers->offsetSet($index, $this->restCartItemExpander->expand($restCartItemTransfer));
        }

        $restCompanyUserCartsRequestTransfer = $this->restCompanyUserCartsRequestMapper->fromRestRequest($restRequest)
            ->setCart($restCartsRequestAttributesTransfer);

        $restCompanyUserCartsResponseTransfer = $this->client->updateQuoteByRestCompanyUserCartsRequest(
            $restCompanyUserCartsRequestTransfer,
        );

        $quoteTransfer = $restCompanyUserCartsResponseTransfer->getQuote();

        if ($quoteTransfer === null || $restCompanyUserCartsResponseTransfer->getIsSuccessful() === false) {
            return $this->restResponseBuilder->buildErrorRestResponse(
                $restCompanyUserCartsResponseTransfer->getErrors()->getArrayCopy(),
            );
        }

        return $this->restResponseBuilder->buildRestResponse($restCompanyUserCartsResponseTransfer->getQuote());
    }
}
