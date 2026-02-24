<?php


namespace BigCommerce\CLI\Resources;

use BigCommerce\CLI\Resources\Resource;

/**
 * Represents a group of resources, which are categorized by a label.
 * Implements the JsonSerializable interface to allow conversion to JSON.
 *
 * @package BigCommerce\CLI\Resources
 */
class Resource_Group implements \JsonSerializable {

    /**
     * @var string The label of the resource group.
     */
    private $label;

    /**
     * @var Resource[] List of resources within the group.
     */
    private $resources = [];

    /**
     * Resource_Group constructor.
     *
     * @param string $label The label for the resource group.
     */
    public function __construct( $label ) {
        $this->label = $label;
    }

    /**
     * Add a resource to the group.
     *
     * @param Resource $resource The resource to add to the group.
     *
     * @return $this The current instance of the group.
     */
    public function add_resource( Resource $resource ) {
        $this->resources[] = $resource;

        return $this;
    }

    /**
     * Get the label of the resource group.
     *
     * @return string The label of the group.
     */
    public function get_label() {
        return $this->label;
    }

    /**
     * Get the list of resources in the group.
     *
     * @return Resource[] The resources within the group.
     */
    public function get_resources() {
        return $this->resources;
    }

    /**
     * Specify data to be serialized to JSON.
     *
     * @return array Data representing the object for JSON encoding.
     */
    public function jsonSerialize() {
        return [
            'label'     => $this->get_label(),
            'resources' => $this->get_resources(),
        ];
    }

}