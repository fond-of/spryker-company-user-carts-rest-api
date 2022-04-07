<?php

namespace FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Builder;

use FondOfSpryker\Glue\CompanyUserCartsRestApi\CompanyUserCartsRestApiConfig;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsAttributesMapperInterface;
use FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestItemsAttributesMapperInterface;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Spryker\Glue\CartsRestApi\CartsRestApiConfig;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestLinkInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class RestResponseBuilder implements RestResponseBuilderInterface
{
    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsAttributesMapperInterface
     */
    protected $restCartsAttributesMapper;

    /**
     * @var \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestItemsAttributesMapperInterface
     */
    protected $restItemsAttributesMapper;

    /**
     * @var \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface
     */
    protected $restResourceBuilder;

    /**
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestCartsAttributesMapperInterface $restCartsAttributesMapper
     * @param \FondOfSpryker\Glue\CompanyUserCartsRestApi\Processor\Mapper\RestItemsAttributesMapperInterface $restItemsAttributesMapper
     * @param \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceBuilderInterface $restResourceBuilder
     */
    public function __construct(
        RestCartsAttributesMapperInterface $restCartsAttributesMapper,
        RestItemsAttributesMapperInterface $restItemsAttributesMapper,
        RestResourceBuilderInterface $restResourceBuilder
    ) {
        $this->restCartsAttributesMapper = $restCartsAttributesMapper;
        $this->restItemsAttributesMapper = $restItemsAttributesMapper;
        $this->restResourceBuilder = $restResourceBuilder;
    }

    /**
     * @param array<\Generated\Shared\Transfer\QuoteErrorTransfer> $quoteErrorTransfers
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function buildNotPersistedRestResponse(array $quoteErrorTransfers): RestResponseInterface
    {
        $detail = 'Undefined';

        if (count($quoteErrorTransfers) > 0) {
            $detail = $quoteErrorTransfers[0]->getMessage();
        }

        $restErrorMessageTransfer = (new RestErrorMessageTransfer())
            ->setCode(1001)
            ->setStatus(Response::HTTP_BAD_REQUEST)
            ->setDetail($detail);

        return $this->restResourceBuilder
            ->createRestResponse()
            ->addError($restErrorMessageTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function buildPersistedRestResponse(QuoteTransfer $quoteTransfer): RestResponseInterface
    {
        $restResource = $this->restResourceBuilder->createRestResource(
            CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USER_CARTS,
            $quoteTransfer->getUuid(),
            $this->restCartsAttributesMapper->fromQuote($quoteTransfer),
        )->setPayload($quoteTransfer);

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $relatedRestResource = $this->restResourceBuilder->createRestResource(
                CartsRestApiConfig::RESOURCE_CART_ITEMS,
                $itemTransfer->getGroupKey(),
                $this->restItemsAttributesMapper->fromItem($itemTransfer),
            )->addLink(
                RestLinkInterface::LINK_SELF,
                sprintf(
                    '%s/%s/%s/%s',
                    CompanyUserCartsRestApiConfig::RESOURCE_CARTS,
                    $restResource->getId(),
                    CompanyUserCartsRestApiConfig::RESOURCE_CART_ITEMS,
                    $itemTransfer->getGroupKey(),
                ),
            );

            $restResource->addRelationship($relatedRestResource);
        }

        $restResource->addLink(
            RestLinkInterface::LINK_SELF,
            sprintf(
                CompanyUserCartsRestApiConfig::FORMAT_SELF_LINK_CART_RESOURCE,
                CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USERS,
                $quoteTransfer->getCompanyUserReference(),
                CompanyUserCartsRestApiConfig::RESOURCE_COMPANY_USER_CARTS,
                $quoteTransfer->getUuid(),
            ),
        );

        return $this->restResourceBuilder->createRestResponse()
            ->addResource($restResource);
    }
}
