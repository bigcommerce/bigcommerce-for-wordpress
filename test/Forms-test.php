<?php

namespace BigCommerce\Container;

use BigCommerce\Forms\Delete_Address_Handler;
use BigCommerce\Forms\Error_Handler;
use BigCommerce\Forms\Form_Redirect;
use BigCommerce\Forms\Messages;
use BigCommerce\Forms\Purchase_Gift_Certificate_Handler;
use BigCommerce\Forms\Product_Review_Handler;
use BigCommerce\Forms\Registration_Handler;
use BigCommerce\Forms\Success_Handler;
use BigCommerce\Forms\Update_Address_Handler;
use BigCommerce\Forms\Update_Profile_Handler;
use BigCommerce\Forms\Switch_Currency_Handler;
use Pimple\Container;

/**
 * Provides form-related functionality for handling various form actions like user registration, address updates,
 * gift certificate purchases, and error handling. Registers form handlers and processes corresponding actions
 * based on incoming requests. This class interacts with the Pimple container for dependency injection.
 */
class Forms extends Provider {
    /**
     * Constant for the delete address form action. It is used to trigger the corresponding handler for address deletion.
     * @var string
     */
    const DELETE_ADDRESS   = 'forms.delete_address';
    /**
     * Constant for the registration form action. It is used to trigger the corresponding handler for user registration.
     * @var string
     */
    const REGISTER         = 'forms.register';

    /**
     * Constant for the product review form action. It is used to trigger the corresponding handler for submitting product reviews.
     * @var string
     */
    const REVIEW           = 'forms.review';

    /**
     * Constant for the update address form action. It is used to trigger the corresponding handler for updating a user's address.
     * @var string
     */
    const UPDATE_ADDRESS   = 'forms.update_address';

    /**
     * Constant for the update profile form action. It is used to trigger the corresponding handler for updating the user's profile.
     * @var string
     */
    const UPDATE_PROFILE   = 'forms.update_profile';

    /**
     * Constant for the gift certificate purchase form action. It is used to trigger the corresponding handler for purchasing a gift certificate.
     * @var string
     */
    const GIFT_CERTIFICATE = 'forms.purchase_gift_certificate';

    /**
     * Constant for the form error handling action. It is used to trigger the corresponding handler for form submission errors.
     * @var string
     */
    const ERRORS           = 'forms.errors';

    /**
     * Constant for the form success handling action. It is used to trigger the corresponding handler for form submission success.
     * @var string
     */
    const SUCCESS          = 'forms.success';

    /**
     * Constant for the form redirects handling action. It is used to trigger the corresponding handler for form submission redirects.
     * @var string
     */
    const REDIRECTS        = 'forms.redirects';

    /**
     * Constant for the messaging handling action. It is used to trigger the corresponding handler for messaging during form submissions.
     *
     * @var string
     */
    const MESSAGING        = 'forms.messaging';

    /**
     * Constant for the switch currency form action. It is used to trigger the corresponding handler for switching currencies during form submissions.
     * @var string
     */
    const SWITCH_CURRENCY  = 'forms.switch_currency';

    /**
     * Registers the form-related actions and handlers.
     * 
     * @param Container $container The container instance used to inject dependencies.
     * 
     * @return void
     */
    public function register(Container $container) {
        $this->actions($container);
        $this->errors($container);
        $this->success($container);
        $this->redirects($container);
        $this->messaging($container);
    }

    /**
     * Registers form actions related to deleting, updating addresses, and handling user registrations.
     *
     * @param Container $container The container instance used to inject dependencies.
     * 
     * @return void
     */
    private function actions(Container $container) {
        add_action('parse_request', $this->create_callback('handle_form_action', function () use ($container) {
            $action = filter_var_array($_REQUEST, ['bc-action' => FILTER_SANITIZE_STRING]);
            if ($action['bc-action']) {
                do_action('bigcommerce/form/action=' . $action['bc-action'], stripslashes_deep($_REQUEST));
            }
        }), 10, 0);

        $container[self::DELETE_ADDRESS] = function (Container $container) {
            return new Delete_Address_Handler();
        };

        add_action('bigcommerce/form/action=' . Delete_Address_Handler::ACTION, $this->create_callback('delete_address', function ($submission) use ($container) {
            $container[self::DELETE_ADDRESS]->handle_request($submission);
        }), 10, 1);

        $container[self::UPDATE_ADDRESS] = function (Container $container) {
            return new Update_Address_Handler();
        };

        add_action('bigcommerce/form/action=' . Update_Address_Handler::ACTION, $this->create_callback('update_address', function ($submission) use ($container) {
            $container[self::UPDATE_ADDRESS]->handle_request($submission);
        }), 10, 1);

        $container[self::UPDATE_PROFILE] = function (Container $container) {
            return new Update_Profile_Handler();
        };

        add_action('bigcommerce/form/action=' . Update_Profile_Handler::ACTION, $this->create_callback('update_profile', function ($submission) use ($container) {
            $container[self::UPDATE_PROFILE]->handle_request($submission);
        }), 10, 1);

        $container[self::REGISTER] = function (Container $container) {
            return new Registration_Handler($container[Compatibility::SPAM_CHECKER], $container[Accounts::LOGIN]);
        };

        add_action('bigcommerce/form/action=' . Registration_Handler::ACTION, $this->create_callback('register', function ($submission) use ($container) {
            return $container[self::REGISTER]->handle_request($submission);
        }), 10, 1);

        $container[self::GIFT_CERTIFICATE] = function (Container $container) {
            return new Purchase_Gift_Certificate_Handler($container[Api::FACTORY]->cart());
        };

        add_action('bigcommerce/form/action=' . Purchase_Gift_Certificate_Handler::ACTION, $this->create_callback('purchase_gift_certificate', function ($submission) use ($container) {
            return $container[self::GIFT_CERTIFICATE]->handle_request($submission);
        }), 10, 1);

        $container[self::REVIEW] = function (Container $container) {
            return new Product_Review_Handler($container[Api::FACTORY]->catalog());
        };

        add_action('bigcommerce/form/action=' . Product_Review_Handler::ACTION, $this->create_callback('product_review', function ($submission) use ($container) {
            $container[self::REVIEW]->handle_request($submission);
        }), 10, 1);
    }

    /**
     * Registers the error handling actions for form submissions.
     * 
     * @param Container $container The container instance used to inject dependencies.
     * 
     * @return void
     */
    private function errors(Container $container) {
        $container[self::ERRORS] = function (Container $container) {
            return new Error_Handler();
        };

        add_action('bigcommerce/form/error', $this->create_callback('error', function (\WP_Error $error, $submission, $redirect = '') use ($container) {
            $container[self::ERRORS]->form_error($error, $submission, $redirect);
        }), 10, 3);
    }
}
