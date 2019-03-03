<?php


namespace BigCommerce\Templates\Option_Types;

use BigCommerce\Templates\Controller;

abstract class Option_Type extends Controller {
	const LABEL    = 'label';
	const OPTIONS  = 'options';
	const ID       = 'id';
	const POST_ID  = 'post_id';
	const REQUIRED = 'required';
	const CONFIG   = 'config';

	protected function parse_options( array $options ) {
		$defaults = [
			self::LABEL    => '',
			self::OPTIONS  => [],
			self::ID       => 0,
			self::REQUIRED => 0,
			self::CONFIG   => [],
		];

		return wp_parse_args( $options, $defaults );
	}

	public function get_data() {
		$data = [
			self::ID       => $this->get_id(),
			self::LABEL    => $this->get_label(),
			self::OPTIONS  => $this->get_options(),
			self::REQUIRED => (bool) $this->options[ self::REQUIRED ],
		];
		$post = get_post();
		if ( $post ) {
			$data[ self::POST_ID ] = $post->ID;
		}

		return $data;
	}

	protected function get_id() {
		return $this->options[ self::ID ];
	}

	protected function get_label() {
		return $this->options[ self::LABEL ];
	}

	protected function get_options() {
		$options = $this->options[ self::OPTIONS ];

		$options = array_map( function ( $option ) {
			return wp_parse_args( $option, $this->default_option() );
		}, $options );

		usort( $options, [ $this, 'sort_options' ] );

		return $options;
	}

	protected function default_option() {
		return [
			'id'         => 0,
			'label'      => '',
			'sort_order' => 0,
			'is_default' => false,
			'value_data' => [],
		];
	}

	protected function sort_options( $a, $b ) {
		if ( $a[ 'sort_order' ] == $b[ 'sort_order' ] ) {
			return 0;
		}

		return ( $a[ 'sort_order' ] < $b[ 'sort_order' ] ) ? - 1 : 1;
	}

}