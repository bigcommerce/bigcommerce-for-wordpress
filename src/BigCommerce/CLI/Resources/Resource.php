<?php


namespace BigCommerce\CLI\Resources;

class Resource implements \JsonSerializable {
	private $url             = '';
	private $name            = '';
	private $description     = '';
	private $thumbnail       = '';
	private $hires_thumbnail = '';
	private $external        = true;
	private $categories      = [];

	/**
	 * @return string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @param string $url
	 *
	 * @return Resource
	 */
	public function set_url( $url ) {
		$this->url = $url;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param string $name
	 *
	 * @return Resource
	 */
	public function set_name( $name ) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @param string $description
	 *
	 * @return Resource
	 */
	public function set_description( $description ) {
		$this->description = $description;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_thumbnail() {
		return $this->thumbnail;
	}

	/**
	 * @param string $thumbnail
	 *
	 * @return Resource
	 */
	public function set_thumbnail( $thumbnail ) {
		$this->thumbnail = $thumbnail;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_hires_thumbnail() {
		return $this->hires_thumbnail;
	}

	/**
	 * @param string $hires_thumbnail
	 *
	 * @return Resource
	 */
	public function set_hires_thumbnail( $hires_thumbnail ) {
		$this->hires_thumbnail = $hires_thumbnail;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function get_external() {
		return $this->external;
	}

	/**
	 * @param bool $external
	 *
	 * @return Resource
	 */
	public function set_external( $external ) {
		$this->external = $external;

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_categories() {
		return $this->categories;
	}

	/**
	 * @param string[] $categories
	 *
	 * @return Resource
	 */
	public function set_categories( array $categories ) {
		$this->categories = $categories;

		return $this;
	}


	public function jsonSerialize() {
		return [
			'name'        => $this->get_name(),
			'description' => $this->get_description(),
			'thumbnail'   => [
				'small' => $this->get_thumbnail(),
				'large' => $this->get_hires_thumbnail(),
			],
			'url'         => $this->get_url(),
			'categories'  => $this->get_categories(),
			'isExternal'  => (bool) $this->get_external(),
		];
	}


}