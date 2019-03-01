<?php


namespace BigCommerce\CLI\Resources;

use BigCommerce\CLI\Resources\Resource;

class Resource_Group implements \JsonSerializable {

	private $label;
	private $resources = [];

	public function __construct( $label ) {
		$this->label = $label;
	}

	public function add_resource( Resource $resource ) {
		$this->resources[] = $resource;

		return $this;
	}

	public function get_label() {
		return $this->label;
	}

	public function get_resources() {
		return $this->resources;
	}

	public function jsonSerialize() {
		return [
			'label'     => $this->get_label(),
			'resources' => $this->get_resources(),
		];
	}

}