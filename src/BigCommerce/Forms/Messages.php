<?php


namespace BigCommerce\Forms;


use BigCommerce\Templates\Message;
use BigCommerce\Templates\Message_Group;

class Messages {

	/**
	 * @param string $content
	 *
	 * @return string The content with messages prepended
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
		 * @param bool $show    true to show messages, false to disable them
		 * @param int  $post_id The ID of the current post
		 */
		if ( ! apply_filters( 'bigcommerce/forms/show_messages', true, $post_id ) ) {
			return $content;
		}

		if ( ! empty( $_REQUEST[ Error_Handler::PARAM ] ) ) {
			$message = $this->get_error_message( $_REQUEST[ Error_Handler::PARAM ] );

			return $message . $content;
		}

		if ( ! empty( $_REQUEST[ Success_Handler::PARAM ] ) ) {
			$message = $this->get_success_message( $_REQUEST[ Success_Handler::PARAM ] );

			return $message . $content;
		}

		return $content;
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

		$messages = [];

		foreach ( $error->get_error_codes() as $code ) {
			foreach ( $error->get_error_messages( $code ) as $content ) {
				$template = new Message( [
					Message::CONTENT => $content,
					Message::KEY     => $code,
					Message::TYPE    => Message::ERROR,
				] );
				$messages[] = $template->render();
			}
		}

		$controller = new Message_Group( [
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

		$template = new Message( [
			Message::CONTENT => $data[ 'message' ],
			Message::TYPE    => Message::SUCCESS,
		] );

		$controller = new Message_Group( [
			Message_Group::MESSAGES => [ $template->render() ],
			Message_Group::TYPE     => Message_Group::SUCCESS,
		] );

		return $controller->render();
	}
}