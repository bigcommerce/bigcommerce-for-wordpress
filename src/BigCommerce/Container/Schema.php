<?php

namespace BigCommerce\Container;

use BigCommerce\Accounts\Roles\Customer;
use BigCommerce\Schema\Queue_Table;
use BigCommerce\Schema\Reviews_Table;
use BigCommerce\Schema\User_Roles;
use Pimple\Container;

/**
 * Manages the registration of database schema-related services and roles.
 *
 * This container handles:
 * - Database table schema for reviews and queues.
 * - User roles, including custom roles like "Customer."
 * - WordPress hooks for table registration and role initialization.
 */
class Schema extends Provider {
    /**
     * Key for the Reviews Table schema service.
     *
     * @var string
     */
    const TABLE_REVIEWS = 'schema.table.reviews';

    /**
     * Key for the Queue Table schema service.
     *
     * @var string
     */
    const TABLE_QUEUES = 'schema.table.queue';

    /**
     * Key for the Role Schema service.
     *
     * @var string
     */
    const ROLE_SCHEMA = 'schema.roles';

    /**
     * Key for the Customer Role service.
     *
     * @var string
     */
    const CUSTOMER_ROLE = 'schema.roles.customer';

    /**
     * Registers database schema and roles into the container.
     *
     * Services registered:
     * - Reviews Table schema.
     * - Queue Table schema.
     * - Customer Role.
     * - User Roles schema.
     *
     * Hooks registered:
     * - `plugins_loaded`: Registers database tables.
     * - `admin_init`: Registers user roles.
     *
     * @param Container $container The DI container for registering services.
     */
    public function register(Container $container) {
        $this->tables($container);
        $this->roles($container);
    }

    /**
     * Registers database table-related services and hooks.
     *
     * Services registered:
     * - `TABLE_REVIEWS`: Handles the schema for the reviews table.
     * - `TABLE_QUEUES`: Handles the schema for the queue table.
     *
     * Hooks registered:
     * - `plugins_loaded`: Triggers table schema registration.
     *
     * @param Container $container The DI container for registering services.
     */
    private function tables(Container $container) {
        /**
         * Registers the Reviews Table schema service.
         *
         * @return Reviews_Table The reviews table schema handler.
         */
        $container[self::TABLE_REVIEWS] = function (Container $container) {
            return new Reviews_Table();
        };

        /**
         * Registers the Queue Table schema service.
         *
         * @return Queue_Table The queue table schema handler.
         */
        $container[self::TABLE_QUEUES] = function (Container $container) {
            return new Queue_Table();
        };

        add_action('plugins_loaded', $this->create_callback('tables_plugins_loaded', function () use ($container) {
            $container[self::TABLE_REVIEWS]->register_tables();
            $container[self::TABLE_QUEUES]->register_tables();
        }), 10, 0);
    }

    /**
     * Registers user role-related services and hooks.
     *
     * Services registered:
     * - `CUSTOMER_ROLE`: Handles the "Customer" user role.
     * - `ROLE_SCHEMA`: Manages user roles and their permissions.
     *
     * Hooks registered:
     * - `admin_init`: Triggers user role registration.
     *
     * @param Container $container The DI container for registering services.
     */
    private function roles(Container $container) {
        /**
         * Registers the Customer Role service.
         *
         * @return Customer The customer role handler.
         */
        $container[self::CUSTOMER_ROLE] = function (Container $container) {
            return new Customer();
        };

        /**
         * Registers the User Roles schema service.
         *
         * @return User_Roles The user roles schema handler.
         */
        $container[self::ROLE_SCHEMA] = function (Container $container) {
            return new User_Roles([
                $container[self::CUSTOMER_ROLE],
            ]);
        };

        add_action('admin_init', $this->create_callback('init_roles', function () use ($container) {
            $container[self::ROLE_SCHEMA]->register_roles();
        }), 10, 0);
    }
}
