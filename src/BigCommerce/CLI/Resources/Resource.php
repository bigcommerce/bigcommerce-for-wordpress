<?php


namespace BigCommerce\CLI\Resources;

/**
 * Class Resource
 *
 * Represents a resource with various properties such as URL, name, description, thumbnails, categories, and external status.
 * Implements the JsonSerializable interface to allow conversion to JSON.
 *
 * @package BigCommerce\CLI\Resources
 */
class Resource implements \JsonSerializable {
    
    /**
     * @var string URL of the resource.
     */
    private $url = '';

    /**
     * @var string Name of the resource.
     */
    private $name = '';

    /**
     * @var string Description of the resource.
     */
    private $description = '';

    /**
     * @var string Thumbnail image URL of the resource.
     */
    private $thumbnail = '';

    /**
     * @var string High-resolution thumbnail image URL of the resource.
     */
    private $hires_thumbnail = '';

    /**
     * @var bool Whether the resource is external or not.
     */
    private $external = true;

    /**
     * @var string[] List of categories associated with the resource.
     */
    private $categories = [];

    /**
     * Get the URL of the resource.
     *
     * @return string The URL of the resource.
     */
    public function get_url() {
        return $this->url;
    }

    /**
     * Set the URL of the resource.
     *
     * @param string $url The URL of the resource.
     * @return Resource The current instance of the resource.
     */
    public function set_url( $url ) {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the name of the resource.
     *
     * @return string The name of the resource.
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Set the name of the resource.
     *
     * @param string $name The name of the resource.
     * @return Resource The current instance of the resource.
     */
    public function set_name( $name ) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the description of the resource.
     *
     * @return string The description of the resource.
     */
    public function get_description() {
        return $this->description;
    }

    /**
     * Set the description of the resource.
     *
     * @param string $description The description of the resource.
     * @return Resource The current instance of the resource.
     */
    public function set_description( $description ) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the thumbnail URL of the resource.
     *
     * @return string The thumbnail URL of the resource.
     */
    public function get_thumbnail() {
        return $this->thumbnail;
    }

    /**
     * Set the thumbnail URL of the resource.
     *
     * @param string $thumbnail The thumbnail URL of the resource.
     * @return Resource The current instance of the resource.
     */
    public function set_thumbnail( $thumbnail ) {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get the high-resolution thumbnail URL of the resource.
     *
     * @return string The high-resolution thumbnail URL of the resource.
     */
    public function get_hires_thumbnail() {
        return $this->hires_thumbnail;
    }

    /**
     * Set the high-resolution thumbnail URL of the resource.
     *
     * @param string $hires_thumbnail The high-resolution thumbnail URL of the resource.
     * @return Resource The current instance of the resource.
     */
    public function set_hires_thumbnail( $hires_thumbnail ) {
        $this->hires_thumbnail = $hires_thumbnail;

        return $this;
    }

    /**
     * Get the external status of the resource.
     *
     * @return bool Whether the resource is external or not.
     */
    public function get_external() {
        return $this->external;
    }

    /**
     * Set the external status of the resource.
     *
     * @param bool $external Whether the resource is external or not.
     * @return Resource The current instance of the resource.
     */
    public function set_external( $external ) {
        $this->external = $external;

        return $this;
    }

    /**
     * Get the categories associated with the resource.
     *
     * @return string[] The list of categories associated with the resource.
     */
    public function get_categories() {
        return $this->categories;
    }

    /**
     * Set the categories associated with the resource.
     *
     * @param string[] $categories The list of categories to associate with the resource.
     * @return Resource The current instance of the resource.
     */
    public function set_categories( array $categories ) {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Specify data to be serialized to JSON.
     *
     * @return array Data representing the object for JSON encoding.
     */
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