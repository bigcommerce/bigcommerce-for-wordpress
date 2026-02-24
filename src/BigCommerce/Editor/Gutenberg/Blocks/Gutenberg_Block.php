<?php


namespace BigCommerce\Editor\Gutenberg\Blocks;

/**
 * A base class for creating Gutenberg blocks in BigCommerce.
 */
abstract class Gutenberg_Block {
	/**
	 * The name of the block. It must follow the Gutenberg
	 * naming convention of [namespace]/[blockname].
	 * 
	 * @var string
	 */
	const NAME = '';

	/**
	 * Gutenberg_Block constructor.
	 *
	 * Ensures that the extending class defines a NAME constant.
	 *
	 * @throws \LogicException If NAME constant is not set in the extending class.
	 */
	public function __construct() {
		if ( static::NAME === '' ) {
			throw new \LogicException( __( 'Gutenberg_Block extending classes must set a NAME constant', 'bigcommerce' ) );
		}
	}

	/**
	 * Returns the name of the block.
	 *
	 * @return string The name of the block.
	 */
	public function name() {
		return static::NAME;
	}

	/**
	 * Registers the block with Gutenberg.
	 *
	 * @return void
	 * @action init
	 */
	public function register() {
		register_block_type( static::NAME, $this->registration_args() );
	}

	/**
	 * Returns the arguments for registering the block.
	 *
	 * @return array The block registration arguments.
	 */
	protected function registration_args() {
		return [
			'render_callback' => [ $this, 'render' ],
			'editor_script'   => 'bigcommerce-gutenberg-scripts',
			'attributes'      => $this->attributes(),
		];
	}

	/**
	 * Render the block to a string. This is called from `do_blocks()`, 
	 * which runs on the `the_content` filter.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string The HTML output of the block.
	 * @see do_blocks()
	 * @see the_content
	 */
	abstract public function render( $attributes );

	/**
	 * Returns the block's attributes.
	 *
	 * @return array The block attributes.
	 */
	protected function attributes() {
		return [];
	}

	/**
	 * Returns the configuration data to pass to the front-end.
	 *
	 * @return array The configuration data.
	 */
	abstract public function js_config();
}