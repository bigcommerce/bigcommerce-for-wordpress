<?php


namespace BigCommerce\Editor\Gutenberg\Blocks;


abstract class Gutenberg_Block {
	/**
	 * @var string The name of the block. It must follow the Gutenberg
	 *             naming convention of [namespace]/[blockname]
	 */
	const NAME = '';

	public function __construct() {
		if ( static::NAME === '' ) {
			throw new \LogicException( __( 'Gutenberg_Block extending classes must set a NAME constant', 'bigcommerce' ) );
		}
	}

	public function name() {
		return static::NAME;
	}

	/**
	 * Register the block with Gutenberg
	 *
	 * @return void
	 * @action init
	 */
	public function register() {
		register_block_type( static::NAME, $this->registration_args() );
	}

	protected function registration_args() {
		return [
			'render_callback' => [ $this, 'render' ],
			'editor_script'   => 'bigcommerce-gutenberg-scripts',
			'attributes'      => $this->attributes(),
		];
	}

	/**
	 * Render the block to a string. Called from
	 * `do_blocks()`, which runs on `the_content` filter
	 *
	 * @param array $attributes
	 *
	 * @return string
	 * @see do_blocks()
	 * @see the_content
	 */
	abstract public function render( $attributes );

	protected function attributes() {
		return [];
	}

	/**
	 * @return array Configuration data to pass to the front-end
	 */
	abstract public function js_config();
}