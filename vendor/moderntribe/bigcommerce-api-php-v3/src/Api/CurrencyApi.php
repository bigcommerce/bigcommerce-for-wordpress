<?php

namespace BigCommerce\Api\v3\Api;

use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\CurrencyAssignmentResponse;

/**
 * CurrencyApi
 *
 * @package  BigCommerce\Api\v3
 */

class CurrencyApi
{
    /**
     * API Client
     *
     * @var \BigCommerce\Api\v3\ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param \BigCommerce\Api\v3\ApiClient $apiClient The api client to use
     */
    public function __construct( \BigCommerce\Api\v3\ApiClient $apiClient )
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return \BigCommerce\Api\v3\ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param \BigCommerce\Api\v3\ApiClient $apiClient set the API client
     *
     * @return CurrencyApi
     */
    public function setApiClient( \BigCommerce\Api\v3\ApiClient $apiClient )
    {
        $this->apiClient = $apiClient;

        return $this;
    }

    /**
     * Operation getChannelCurrencyAssignments
     * Get Channel Currency Assignments
     *
     *
     * @param int $channel_id The ID of a Channel that&#39;s available through GET /channels (required)
     * @param array $params = []
     * @return CurrencyAssignmentResponse
     * @throws \BigCommerce\Api\v3\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     */
    public function getChannelCurrencyAssignments( $channel_id, array $params = [])
    {
        list($response) = $this->getChannelCurrencyAssignmentsHttpInfo( $channel_id, $params );
        return $response;
    }

    /**
     * Operation getChannelCurrencyAssignmentsHttpInfo
     *
     * @param int $channel_id The ID of a Channel that&#39;s available through GET /channels (required)
     * @param array $params = []
     * @throws \BigCommerce\Api\v3\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \BigCommerce\Api\v3\Model\CurrencyAssignmentResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function getChannelCurrencyAssignmentsHttpInfo( $channel_id, array $params = [] )
    {
        // verify the required parameter 'channel_id' is set
        if ( ! isset( $channel_id ) ) {
            throw new \InvalidArgumentException( 'Missing the required parameter $channel_id when calling getChannelCurrencyAssignments' );
        }

        $resourcePath = "/channels/{channelId}/currency-assignments";
        $httpBody     = '';
        $queryParams  = [];
        $headerParams = [];
        $formParams   = [];

        $_header_accept = $this->apiClient->selectHeaderAccept( ['application/json'] );
        if ( ! is_null( $_header_accept ) ) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType( ['application/json'] );

        // query params
        foreach ( $params as $key => $param ) {
            $queryParams[ $key ] = $this->apiClient->getSerializer()->toQueryValue( $param );
        }

        if ( isset( $channel_id ) ) {
            $resourcePath = str_replace(
                "{" . "channelId" . "}",
                $this->apiClient->getSerializer()->toPathValue( $channel_id ),
                $resourcePath
            );
        }

        $resourcePath = str_replace( "{format}", "json", $resourcePath );

        // for model (json/xml)
        if ( isset( $_tempBody ) ) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif ( count( $formParams ) > 0 ) {
            $httpBody = $formParams; // for HTTP post (form)
        }

        // make the API Call
        try {
            list( $response, $statusCode, $httpHeader ) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\BigCommerce\Api\v3\Model\CurrencyAssignmentResponse',
                '/channels/{channelId}/currency-assignments'
            );

            return [
                $this->apiClient->getSerializer()->deserialize( $response, '\BigCommerce\Api\v3\Model\CurrencyAssignmentResponse', $httpHeader ),
                $statusCode,
                $httpHeader
            ];

        } catch ( ApiException $e ) {
            switch ( $e->getCode() ) {

                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize(
                        $e->getResponseBody(),
                        '\BigCommerce\Api\v3\Model\CurrencyAssignments',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject( $data );
                    break;

            }

            throw $e;
        }
    }

}
