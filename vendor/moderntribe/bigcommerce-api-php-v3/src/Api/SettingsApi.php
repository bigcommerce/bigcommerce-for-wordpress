<?php

namespace BigCommerce\Api\v3\Api;

use BigCommerce\Api\v3\ApiClient;
use BigCommerce\Api\v3\ApiException;

class SettingsApi
{
    /**
     * API Client
     *
     * @var ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param ApiClient $apiClient The api client to use
     */
    public function __construct( ApiClient $apiClient )
    {
        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param ApiClient $apiClient set the API client
     *
     * @return SettingsApi
     */
    public function setApiClient( ApiClient $apiClient )
    {
        $this->apiClient = $apiClient;

        return $this;
    }

    /**
     * @param int $channel_id
     * @return mixed
     * @throws ApiException
     */
    public function getStorefrontProfile( $channel_id = 0 )
    {
        list( $response ) = $this->getStorefrontHttpInfo( '/settings/store/profile', $channel_id );

        return $response;
    }

    /**
     * @param int $channel_id
     * @param array $data
     * @return mixed
     * @throws ApiException
     */
    public function updateStorefrontProfile( $channel_id = 0, $data = [] )
    {
        list( $response ) = $this->updateWithHttpInfo( '/settings/store/profile', $channel_id, $data );

        return $response;
    }

    /**
     * @param int $channel_id
     * @return mixed
     * @throws ApiException
     */
    public function getStorefrontProduct( $channel_id = 0 )
    {
        list( $response ) = $this->getStorefrontHttpInfo( '/settings/storefront/product', $channel_id );

        return $response;
    }

    /**
     * @param int $channel_id
     * @param array $data
     * @return mixed
     * @throws ApiException
     */
    public function updateStorefrontProduct( $channel_id = 0, $data = [] )
    {
        list( $response ) = $this->updateWithHttpInfo( '/settings/storefront/product', $channel_id, $data );

        return $response;
    }

    /**
     * @param int $channel_id
     * @return mixed
     * @throws ApiException
     */
    public function getStorefrontStatus( $channel_id = 0 )
    {
        list( $response ) = $this->getStorefrontHttpInfo( '/settings/storefront/status', $channel_id );

        return $response;
    }

	public function getStoreAnalyticsSettings( $channel_id = 0 ) {
		list( $response ) = $this->getStorefrontHttpInfo( '/settings/analytics', $channel_id );

		return $response;
	}

    /**
     * @param int $channel_id
     * @param array $data
     * @return mixed
     * @throws ApiException
     */
    public function updateStorefrontStatus( $channel_id = 0, $data = [] )
    {
        list( $response ) = $this->updateWithHttpInfo( '/settings/storefront/status', $channel_id, $data );

        return $response;
    }

    /**
     * @param int $channel_id
     * @return array
     * @throws ApiException
     */
    public function getStorefrontHttpInfo( $resourcePath = '', $channel_id = 0 ) {
        // verify the required parameter 'channel_id' is set
        if ( ! isset( $channel_id ) ) {
            throw new \InvalidArgumentException('Missing the required parameter $channel_id when calling getStorefrontProfile');
        }

        $httpBody       = [];
        $queryParams    = [];
        $headerParams   = [];
        $_header_accept = $this->apiClient->selectHeaderAccept( ['application/json'] );

        if ( ! is_null( $_header_accept ) ) {
            $headerParams['Accept'] = $_header_accept;
        }

        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        if ( ! empty( $channel_id ) ) {
            $resourcePath = sprintf( '%s?channel_id=%d', $resourcePath, $channel_id );
        }

        // default format to json
        $resourcePath = str_replace("{format}", "json", $resourcePath);

        // make the API Call
        try {
            list( $response, $statusCode, $httpHeader ) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                null,
                $resourcePath
            );
            return [ $response, $statusCode, $httpHeader, ];

        } catch (ApiException $e) {
            switch ($e->getCode()) {

                case 201:
                    $e->setResponseObject($e->getResponseBody());
                    break;

            }

            throw $e;
        }
    }

    public function updateWithHttpInfo( $resourcePath = '', $channel_id = 0 , $body = [] )
    {
        // verify the required parameter 'channel_id' is set
        if ( ! isset( $channel_id ) ) {
            throw new \InvalidArgumentException('Missing the required parameter $channel_id when calling updateStorefront');
        }

        // verify the required parameter 'body' is set
        if ( ! isset( $body ) ) {
            throw new \InvalidArgumentException('Missing the required parameter $body when calling updateStorefront');
        }

        $queryParams    = [];
        $headerParams   = [];
        $_header_accept = $this->apiClient->selectHeaderAccept( ['application/json'] );
        if ( ! is_null( $_header_accept ) ) {
            $headerParams['Accept'] = $_header_accept;
        }

        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType( ['application/json'] );

        $httpBody = $body;

        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'PUT',
                $queryParams,
                $httpBody,
                $headerParams,
                null,
                $resourcePath
            );
            return [ $response, $statusCode, $httpHeader, ];

        } catch ( ApiException $e ) {
            switch ( $e->getCode() ) {
                case 201:
                    $e->setResponseObject( $e->getResponseBody() );
                    break;

            }

            throw $e;
        }
    }
}
