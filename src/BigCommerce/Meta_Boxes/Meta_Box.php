<?php


namespace BigCommerce\Meta_Boxes;


abstract class Meta_Box {

	/**
	 *
	 * @return void
	 * @action load-{$page_hook}
	 */
	public function register() {
		add_meta_box(
			$this->get_name(),
			$this->get_title(),
			[ $this, 'render' ],
			$this->get_screen(),
			$this->get_context(),
			$this->get_priority(),
			$this->get_callback_args()
		);
	}

	/**
	 * @return string The unique identifier for the metabox
	 */
	abstract protected function get_name();

	/**
	 * @return string The title of the metabox
	 */
	abstract protected function get_title();

	/**
	 * Render the metabox contents
	 *
	 * @param object $object
	 *
	 * @return void
	 */
	abstract public function render( $object );

	/**
	 * @return string|array|null The screens on which to display the metabox
	 */
	protected function get_screen() {
		return null;
	}

	/**
	 * @return string One of 'normal', 'side', or 'advanced'
	 */
	protected function get_context() {
		return 'advanced';
	}

	/**
	 * @return string One of 'high', 'core', 'default', 'low'
	 */
	protected function get_priority() {
		return 'default';
	}

	/**
	 * @return null|array Additional args to pass to the render callback
	 */
	protected function get_callback_args() {
		return null;
	}

}
