<?php


namespace BigCommerce\Api;


use BigCommerce\Api\v3\ApiException;

/**
 * A specific exception type that extends `ApiException` to signal
 * that a required configuration is missing when making API requests.
 *
 * This allows for more granular exception handling within the BigCommerce API SDK.
 *
 * @package BigCommerce\Api
 *
 * @extends \BigCommerce\Api\v3\ApiException
 */
class ConfigurationRequiredException extends ApiException {}