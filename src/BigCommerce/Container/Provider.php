<?php

/**
 * This abstract class implements the `ServiceProviderInterface` from Pimple. It provides functionality for managing
 * callback registrations and offers a mechanism to access callbacks dynamically through the `__get` magic method.
 * Subclasses are expected to define their own service registration logic.
 */
abstract class Provider implements ServiceProviderInterface {

    /**
     * @var array $callbacks Holds a collection of registered callbacks identified by unique identifiers.
     */
    protected $callbacks = [];

    /**
     * Magic method to retrieve registered callbacks by their identifier.
     *
     * This method allows access to the registered callbacks using property syntax. If the requested property is found
     * in the `$callbacks` array, it returns the corresponding callback; otherwise, it returns `null`.
     *
     * @param string $property The identifier of the callback to retrieve.
     *
     * @return callable|null The registered callback, or `null` if not found.
     */
    public function __get( $property ) {
        if ( array_key_exists( $property, $this->callbacks ) ) {
            return $this->callbacks[ $property ];
        }
        return null;
    }

    /**
     * Creates and registers a callback for a specific identifier.
     *
     * This method ensures that a callback is not already registered under the same identifier. If the identifier is
     * already in use, an exception is thrown. Otherwise, it adds the callback to the `$callbacks` array.
     *
     * @param string $identifier The unique identifier for the callback.
     * @param callable $callback The callback function to register.
     *
     * @throws \InvalidArgumentException If the identifier is already in use.
     *
     * @return callable The registered callback.
     */
    protected function create_callback( $identifier, callable $callback ) {
        if ( array_key_exists( $identifier, $this->callbacks ) ) {
            throw new \InvalidArgumentException( sprintf( __( 'Invalid identifier: %s has already been set.', 'bigcommerce' ), $identifier ) );
        }
        $this->callbacks[ $identifier ] = $callback;
        return $callback;
    }
}