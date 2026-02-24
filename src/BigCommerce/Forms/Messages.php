<?php


namespace BigCommerce\Forms;


use BigCommerce\Templates\Message;
use BigCommerce\Templates\Message_Group;

/**
 * Handles rendering of messages (success or error) above forms or within form submissions.
 */
class Messages {

	/**
	 * Renders messages above the content.
	 *
	 * This method filters whether to show the error/success messages above the content of the form.
	 *
	 * @param string $content The original content.
	 *
	 * @return string The content with messages prepended.
	 * @filter the_content 5
	 */
	public function render_messages_above_content( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}
		$post_id = get_queried_object_id();
		if ( $post_id !== get_the_ID() ) {
			return $content; // don't filter if we're not on the main post
		}

		/**
		 * Filter whether to show error/success messages above forms. If disabled,
		 * the theme or plugin should show them elsewhere.
		 *
		 * @param bool $show    true to show messages, false to disable them.
		 * @param int  $post_id The ID of the current post.
		 */
		if ( ! apply_filters( 'bigcommerce/forms/show_messages', true, $post_id ) ) {
			return $content;
		}

		/**
		 * Filter the feedback messages that will be rendered.
		 *
		 * @param string $messages The rendered messages.
		 */
		$messages = apply_filters( 'bigcommerce/forms/messages', '' );

		return $messages . $content;
	}

	/**
	 * Renders the error or success messages for the form submission.
	 *
	 * This method checks for stored error or success messages and renders them if available.
	 *
	 * @return string The rendered error or success messages.
	 */
	public function render_messages() {
		$bc_error = filter_var_array( $_REQUEST, [ Error_Handler::PARAM => FILTER_SANITIZE_STRING ] );
		if ( $bc_error[ Error_Handler::PARAM ] ) {
			return $this->get_error_message( $bc_error[ Error_Handler::PARAM ] );
		}

		$bc_success = filter_var_array( $_REQUEST, [ Success_Handler::PARAM => FILTER_SANITIZE_STRING ] );
		if ( $bc_success[ Success_Handler::PARAM ] ) {
			return $this->get_success_message( $bc_success[ Success_Handler::PARAM ] );
		}

		return '';
	}

	private function get_error_message( $key ) {
		$data = get_transient( $key );
		if ( empty( $data[ 'error' ] ) || ! array_key_exists( 'user_id', $data ) ) {
			return '';
		}
		if ( $data[ 'user_id' ] != get_current_user_id() ) {
			return '';
		}
		/** @var \WP_Error $error */
		$error = $data[ 'error' ];
		if ( ! is_wp_error( $error ) || count( $error->get_error_messages() ) < 1 ) {
			return '';
		}

		/**
		 * Filter whether to show error messages above forms. If disabled,
		 * the theme or plugin should show them elsewhere.
		 *
		 * @param bool  $show true to show messages, false to disable them.
		 * @param array $data Data for the error messages.
		 */
		if ( ! apply_filters( 'bigcommerce/forms/show_error_messages', true, $data ) ) {
			return '';
		}

		$messages = [];

		foreach ( $error->get_error_codes() as $code ) {
			foreach ( $error->get_error_messages( $code ) as $content ) {
				$template   = Message::factory( [
					Message::CONTENT => $content,
					Message::KEY     => $code,
					Message::TYPE    => Message::ERROR,
				] );
				$messages[] = $template->render();
			}
		}

		$controller = Message_Group::factory( [
			Message_Group::MESSAGES => $messages,
			Message_Group::TYPE     => Message_Group::ERROR,
		] );

		return $controller->render();
	}

	private function get_success_message( $key ) {

		$data = get_transient( $key );
		if ( empty( $data[ 'message' ] ) || ! array_key_exists( 'user_id', $data ) ) {
			return '';
		}
		if ( $data[ 'user_id' ] != get_current_user_id() ) {
			return '';
		}

		/**
		 * Filter whether to show success messages above forms. If disabled,
		 * the theme or plugin should show them elsewhere.
		 *
		 * @param bool  $show true to show messages, false to disable them.
		 * @param array $data Data for the success message.
		 */
		if ( ! apply_filters( 'bigcommerce/forms/show_success_messages', true, $data ) ) {
			return '';
		}

		$args = [
			Message::CONTENT    => $data[ 'message' ],
			Message::TYPE       => Message::SUCCESS,
			Message::KEY        => empty( $data[ 'data' ][ 'key' ] ) ? '' : $data[ 'data' ][ 'key' ],
			Message::ATTRIBUTES => [],
		];

		/**
		 * Filter the arguments passed to the success message template.
		 *
		 * @param array $args The arguments that will be passed.
		 * @param array $data The data that was stored with the message.
		 */
		$args = apply_filters( 'bigcommerce/messages/success/arguments', $args, $data );

		$template = Message::factory( $args );

		$controller = Message_Group::factory( [
			Message_Group::MESSAGES => [ $template->render() ],
			Message_Group::TYPE     => Message_Group::SUCCESS,
		] );

		return $controller->render();
	}
}