<?php


namespace BigCommerce\Forms;


use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiException;
use BigCommerce\Api\v3\Model\ProductReviewPost;
use BigCommerce\Post_Types\Product\Product;

class Product_Review_Handler implements Form_Handler {
    /**
     * Action constant used for identifying the form submission.
	 * @var string
     */
    const ACTION = 'product-review';

    /**
     * @var CatalogApi
     */
    private $api;

    /**
     * Product_Review_Handler constructor.
     *
     * @param CatalogApi $api The API instance to interact with BigCommerce's catalog.
     */
    public function __construct( CatalogApi $api ) {
        $this->api = $api;
    }

    /**
     * Handle the form submission for product reviews.
     *
     * @param array $submission The form submission data.
     *
     * @return void
     * @action bigcommerce/form/error Triggered when a form has errors that prevent completion.
     * @action bigcommerce/form/success Triggered when the form has been successfully submitted.
     */
    public function handle_request( $submission ) {

        if ( ! $this->should_handle_request( $submission ) ) {
            return;
        }

        $errors = $this->validate_submission( $submission );

        if ( count( $errors->get_error_codes() ) > 0 ) {

            /**
             * Triggered when a form has errors that prevent completion.
             *
             * @param \WP_Error $errors     The message that will display to the user
             * @param array     $submission The data submitted to the form
             */
            do_action( 'bigcommerce/form/error', $errors, $submission );
			
            return;
        }

        $product    = new Product( (int) $submission['bc-review']['post_id'] );
        $product_id = $product->bc_id();

        $review_request = new ProductReviewPost( [
            'title'         => sanitize_text_field( $submission['bc-review']['subject'] ),
            'text'          => sanitize_textarea_field( $submission['bc-review']['content'] ),
			/**
			 * Filters review status in form.
			 *
			 * @param string $status Status.
			 */
            'status'        => apply_filters( 'bigcommerce/form/review/status', 'pending', $submission, $product_id ),
            'rating'        => intval( $submission['bc-review']['rating'] ),
            'email'         => sanitize_email( $submission['bc-review']['email'] ),
            'name'          => sanitize_text_field( $submission['bc-review']['name'] ),
            'date_reviewed' => new \DateTime(),
        ] );

        try {
            $response = $this->api->createProductReview( $product_id, $review_request );
        } catch ( ApiException $e ) {
            $errors->add( 'api_error', __( 'There was an error saving your review. Please try again.', 'bigcommerce' ) );
            /**
             * Triggered when a form has errors that prevent completion.
             *
             * @param \WP_Error $errors     The message that will display to the user
             * @param array     $submission The data submitted to the form
             */
            do_action( 'bigcommerce/form/error', $errors, $submission );
            
            return;
        }

        /**
         * Filters review form created message.
         *
         * @param string $message Message displayed after review submission.
         */
        $message = apply_filters( 'bigcommerce/form/review/created_message', __( 'Thank you for your review! It has been successfully submitted and is pending.', 'bigcommerce' ) );
        /**
         * Triggered when the form has been successfully submitted.
         *
         * @param string $message    The success message to display to the user
         * @param array  $submission The data submitted to the form
         * @param mixed  $data      Additional data related to the submission (null in this case)
         */
        do_action( 'bigcommerce/form/success', $message, $submission, null );
    }

    private function should_handle_request( $submission ) {
        if ( empty( $submission['bc-action'] ) || $submission['bc-action'] !== self::ACTION ) {
            return false;
        }
        if ( empty( $submission['_wpnonce'] ) || empty( $submission['bc-review'] ) || empty( $submission['bc-review']['post_id'] ) ) {
            return false;
        }

        /**
         * This filter is documented in src/BigCommerce/Templates/Product_Reviews.php
         */
        if ( ! apply_filters( 'bigcommerce/product/reviews/show_form', is_user_logged_in(), $submission['bc-review']['post_id'] ) ) {
            return false;
        }

        return true;
    }

    private function validate_submission( $submission ) {
        $errors = new \WP_Error();

        if ( ! wp_verify_nonce( $submission['_wpnonce'], self::ACTION . $submission['bc-review']['post_id'] ) ) {
            $errors->add( 'invalid_nonce', __( 'There was an error validating your request. Please try again.', 'bigcommerce' ) );
        }

        if ( empty( $submission['bc-review']['rating'] ) || intval( $submission['bc-review']['rating'] ) < 1 ) {
            $errors->add( 'rating', __( 'Please select a rating.', 'bigcommerce' ) );
        }
        if ( empty( $submission['bc-review']['name'] ) ) {
            $errors->add( 'name', __( 'Name is required.', 'bigcommerce' ) );
        }
        if ( empty( $submission['bc-review']['email'] ) ) {
            $errors->add( 'email', __( 'Email Address is required.', 'bigcommerce' ) );
        } elseif ( ! is_email( $submission['bc-review']['email'] ) ) {
            $errors->add( 'email', __( 'Please verify that you have submitted a valid email address.', 'bigcommerce' ) );
        }
        if ( empty( $submission['bc-review']['subject'] ) ) {
            $errors->add( 'subject', __( 'Please give your review a subject.', 'bigcommerce' ) );
        }
        if ( empty( $submission['bc-review']['content'] ) ) {
            $errors->add( 'content', __( 'Please add comments to your review.', 'bigcommerce' ) );
        }

        /**
         * Filters update review form errors.
         *
         * @param \WP_Error $errors     WP error.
         * @param array     $submission Submitted data.
         */
        $errors = apply_filters( 'bigcommerce/form/review/errors', $errors, $submission );

        return $errors;
    }

    /**
     * Do not show product review form error/success messages above
     * the post content. They will be rendered with the form.
     *
     * @param bool $show Whether to show messages above content.
     * @param int  $post_id The post ID to check.
     *
     * @return bool Whether to show the messages.
     * @filter bigcommerce/forms/show_messages This filter controls the visibility of form messages.
     */
    public function remove_form_messages_from_post_content( $show, $post_id ) {
        $data       = [];
        $bc_error   = filter_var_array( $_REQUEST, [ Error_Handler::PARAM => FILTER_SANITIZE_STRING ] );
        $bc_success = filter_var_array( $_REQUEST, [ Success_Handler::PARAM => FILTER_SANITIZE_STRING ] );

        if ( $bc_error[ Error_Handler::PARAM ] ) {
            $data = get_transient( $bc_error[ Error_Handler::PARAM ] );
        } elseif ( $bc_success[ Success_Handler::PARAM ] ) {
            $data = get_transient( $bc_success[ Success_Handler::PARAM ] );
        }

        if ( $data && array_key_exists( 'submission', $data ) ) {
            if ( array_key_exists( 'bc-action', $data['submission'] ) ) {
                if ( $data['submission']['bc-action'] == self::ACTION ) {
                    return false;
                }
            }
        }

        return $show;
    }

    /**
     * Handle the visibility of the product reviews form.
     *
     * @param bool $enabled Whether the form is enabled.
     * @param int  $post_id The post ID to check.
     *
     * @return bool Whether to display the reviews form.
     * @filter bigcommerce/product/reviews/show_form This filter controls the availability of the reviews form.
     */
    public function toggle_reviews_form_availability( bool $enabled, int $post_id ): bool {
        $is_comments_allowed_globally = get_option( 'default_comment_status' ) === 'open';
        $user_should_be_logged_in     = ( int ) get_option( 'comment_registration' ) === 1;

        // If comments are allowed and users can post them without registration
        if ( $is_comments_allowed_globally && ! $user_should_be_logged_in ) {
            return comments_open( $post_id );
        }

        // If commenting is enabled for registered users only
        if ( $is_comments_allowed_globally && $user_should_be_logged_in ) {
            return is_user_logged_in();
        }

        // Return default value
        return $enabled;
    }
}
