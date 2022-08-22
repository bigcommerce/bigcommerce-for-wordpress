<?php

namespace BigCommerce\Api\v3\Model;

use \ArrayAccess;

class GQL_Term_Model implements ArrayAccess {

	const MAX_NAME_LENGTH = 75;
	/**
	 * The original name of the model.
	 * @var string
	 */
	protected static $swaggerModelName = 'GQLTerm';

	/**
	 * Array of property to type mappings. Used for (de)serialization
	 * @var string[]
	 */
	protected static $swaggerTypes = [
		'parent_id'   => 'int',
		'name'        => 'string',
		'description' => 'string',
		'image_url'   => 'string',
		'custom_url'  => 'string',
		'id'          => 'int',
	];

	public static function swaggerTypes()
	{
		return self::$swaggerTypes;
	}

	/**
	 * Array of attributes where the key is the local name, and the value is the original name
	 * @var string[]
	 */
	protected static $attributeMap = [
		'parent_id'   => 'parent_id',
		'name'        => 'name',
		'description' => 'description',
		'image_url'   => 'image_url',
		'custom_url'  => 'custom_url',
		'id'          => 'id',
	];

	/**
	 * Array of attributes to setter functions (for deserialization of responses)
	 * @var string[]
	 */
	protected static $setters = [
		'parent_id'   => 'setParentId',
		'name'        => 'setName',
		'description' => 'setDescription',
		'image_url'   => 'setImageUrl',
		'custom_url'  => 'setCustomUrl',
		'id'          => 'setId',
	];

	/**
	 * Array of attributes to getter functions (for serialization of requests)
	 * @var string[]
	 */
	protected static $getters = [
		'parent_id'   => 'getParentId',
		'name'        => 'getName',
		'description' => 'getDescription',
		'image_url'   => 'getImageUrl',
		'custom_url'  => 'getCustomUrl',
		'id'          => 'getId',
	];

	public static function attributeMap()
	{
		return self::$attributeMap;
	}

	public static function setters()
	{
		return self::$setters;
	}

	public static function getters()
	{
		return self::$getters;
	}

	private $container = [];

	public function __construct( \StdClass $data = null ) {
		$this->container['parent_id']   = $data->parent_id ?? 0;
		$this->container['name']        = $data->name ?? null;
		$this->container['description'] = $data->description ?? null ;
		$this->container['image_url']   = $data->image_url ?? null;
		$this->container['custom_url']  = $data->path ?? null;
		$this->container['id']          = $data->entityId ?? null;
	}

	public function offsetSet( $offset, $value ) {
		if ( is_null( $offset ) ) {
			$this->container[] = $value;
		} else {
			$this->container[ $offset ] = $value;
		}
	}

	public function offsetExists( $offset ) {
		return isset( $this->container[ $offset ] );
	}

	public function offsetUnset( $offset ) {
		unset( $this->container[ $offset ] );
	}

	public function offsetGet( $offset ) {
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}

	/**
	 * Gets parent_id
	 * @return int
	 */
	public function getParentId()
	{
		return $this->container['parent_id'];
	}

	/**
	 * Sets parent_id
	 * @param int $parent_id The unique numeric ID of the term's parent. This field controls where the term sits in the tree of terms that organize the catalog.
	 * @return $this
	 */
	public function setParentId( $parent_id )
	{
		$this->container['parent_id'] = $parent_id;

		return $this;
	}

	/**
	 * Gets name
	 * @return string
	 */
	public function getName()
	{
		return $this->container['name'];
	}

	/**
	 * Sets name
	 * @param string $name The name displayed for the term. Name is unique with respect to the term's siblings.
	 * @return $this
	 */
	public function setName( $name )
	{
		if ( strlen( $name ) > self::MAX_NAME_LENGTH ) {
			throw new \InvalidArgumentException( sprintf( 'invalid length for $name when calling term., must be smaller than or equal to %d.', self::MAX_NAME_LENGTH ) );
		}
		if ( strlen( $name ) < 1 ) {
			throw new \InvalidArgumentException( 'invalid length for $name when calling term., must be bigger than or equal to 1.' );
		}
		$this->container['name'] = $name;

		return $this;
	}

	/**
	 * Gets description
	 * @return string
	 */
	public function getDescription()
	{
		return $this->container['description'];
	}

	/**
	 * Sets description
	 * @param string $description The product description, which can include HTML formatting.
	 * @return $this
	 */
	public function setDescription( $description )
	{
		$this->container['description'] = $description;

		return $this;
	}

	/**
	 * Gets image_url
	 * @return string
	 */
	public function getImageUrl()
	{
		return $this->container['image_url'];
	}

	/**
	 * Sets image_url
	 * @param string $image_url Image URL used for this term on the storefront. Images can be uploaded via form file post to `/categories/{termId}/image`, or by providing a publicly accessible URL in this field.
	 * @return $this
	 */
	public function setImageUrl( $image_url )
	{
		$this->container['image_url'] = $image_url;

		return $this;
	}

	/**
	 * Gets image_url
	 * @return string
	 */
	public function getCustomUrl()
	{
		return $this->container['custom_url'];
	}

	/**
	 * Sets image_url
	 * @param string $custom_url Image URL used for this term on the storefront. Images can be uploaded via form file post to `/categories/{termId}/image`, or by providing a publicly accessible URL in this field.
	 * @return $this
	 */
	public function setCustomUrl( $custom_url )
	{
		$this->container['custom_url'] = $custom_url;

		return $this;
	}

	/**
	 * Gets id
	 * @return int
	 */
	public function getId()
	{
		return $this->container['id'];
	}

	/**
	 * Sets id
	 * @param int $id The unique numeric ID of the term; increments sequentially.
	 *
	 * @return $this
	 */
	public function setId( $id )
	{
		$this->container['id'] = $id;

		return $this;
	}

	/**
	 * Gets the string presentation of the object
	 * @return string
	 */
	public function __toString()
	{
		if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
			return json_encode(\BigCommerce\Api\v3\ObjectSerializer::sanitizeForSerialization($this), JSON_PRETTY_PRINT);
		}

		return json_encode(\BigCommerce\Api\v3\ObjectSerializer::sanitizeForSerialization($this));
	}

}
