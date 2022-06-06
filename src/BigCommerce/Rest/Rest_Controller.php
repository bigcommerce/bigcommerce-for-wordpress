<?php


namespace BigCommerce\Rest;


class Rest_Controller extends \WP_REST_Controller {
	protected $namespace_base;
	protected $version;

	/**
	 * Rest_Controller constructor.
	 *
	 * @param string $namespace_base
	 * @param string $version
	 * @param string $rest_base
	 */
	public function __construct( $namespace_base, $version, $rest_base ) {
		$this->namespace_base = $namespace_base;
		$this->version        = $version;

		$this->namespace = $this->get_namespace();
		$this->rest_base = $rest_base;
	}

	protected function get_namespace() {
		return $this->namespace_base . '/v' . $this->version;
	}

	public function get_base_url() {
		return rest_url() . $this->namespace . '/' . $this->rest_base;
	}

	protected function parse_result( $response, $client ) {
		$result['data'] = json_decode( '[' . $client->getSerializer()->serializeCollection( $response->getData(), 'multi' ) . ']' );
		$result['meta'] = json_decode( '[' . $response->getMeta()->__toString() . ']' );

		if ( ! empty( $result['meta'] ) ) {
			$result['meta'] = $result['meta'][0];
		}

		return rest_ensure_response( $result );
	}
}
