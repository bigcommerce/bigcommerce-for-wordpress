<?php


namespace BigCommerce\Settings\Sections;


use BigCommerce\Settings\Screens\Settings_Screen;
use BigCommerce\Taxonomies\Channel\Channel;

/**
 * Class Channels
 *
 * Channel configuration after the plugin has been fully configured,
 * which at this point is limited to changing the channel name
 */
class Channels extends Settings_Section {
	const NAME         = 'channel';
	const CHANNEL_ID   = 'bigcommerce_channel_id';
	const CHANNEL_NAME = 'bigcommerce_channel_name';
	const NEW_NAME     = 'bigcommerce_add_channel_name';

	const PRIMARY_CHANNEL    = 'bigcommerce_primary_channel';
	const CONNECTED_CHANNELS = 'bigcommerce_connected_channels';
	const OTHER_CHANNELS     = 'bigcommerce_other_channels';

	const ACTION_RENAME     = 'rename';
	const ACTION_CONNECT    = 'connect';
	const ACTION_DISCONNECT = 'disconnect';
	const ACTION_PROMOTE    = 'promote';
	const POST_ACTION       = 'bigcommerce-channel-operation';

	public function register_settings_section() {

		add_settings_section(
			self::NAME,
			__( 'Channel', 'bigcommerce' ),
			'__return_false',
			Settings_Screen::NAME
		);

		$multichannel = Channel::multichannel_enabled();

		add_settings_field(
			self::PRIMARY_CHANNEL,
			$multichannel ? esc_html( __( 'Primary Channel', 'bigcommerce' ) ) : esc_html( __( 'Channel Name', 'bigcommerce' ) ),
			[ $this, 'render_primary_channel' ],
			Settings_Screen::NAME,
			self::NAME
		);

		register_setting(
			Settings_Screen::NAME,
			self::CHANNEL_NAME,
			[
				'sanitize_callback' => [ $this, 'save_channel_names' ],
			]
		);

		if ( $multichannel ) {
			add_settings_field(
				self::CONNECTED_CHANNELS,
				esc_html( __( 'Connected Channels', 'bigcommerce' ) ),
				[ $this, 'render_connected_channels' ],
				Settings_Screen::NAME,
				self::NAME
			);

			add_settings_field(
				self::OTHER_CHANNELS,
				esc_html( __( 'Other Channels', 'bigcommerce' ) ),
				[ $this, 'render_other_channels' ],
				Settings_Screen::NAME,
				self::NAME
			);

			register_setting(
				Settings_Screen::NAME,
				self::NEW_NAME
			);
		}
	}

	public function render_primary_channel() {
		$terms = $this->get_channel_terms( Channel::STATUS_PRIMARY );

		if ( empty( $terms ) ) {
			printf( '<p>%s</p>', esc_html( __( 'Primary channel is not set. Please set one now.', 'bigcommerce' ) ) );

			return;
		}

		foreach ( $terms as $term ) { // there should be only one, but let's be flexible
			$this->render_channel_row( $term, Channel::STATUS_PRIMARY, [
				self::ACTION_RENAME => __( 'Rename', 'bigcommerce' ),
			] );
		}
	}

	public function render_connected_channels() {
		$terms = $this->get_channel_terms( Channel::STATUS_CONNECTED );

		if ( empty( $terms ) ) {
			echo '&mdash;';

			return;
		}

		foreach ( $terms as $term ) {
			$this->render_channel_row( $term, Channel::STATUS_CONNECTED, [
				self::ACTION_RENAME     => __( 'Rename', 'bigcommerce' ),
				self::ACTION_PROMOTE    => __( 'Make Primary', 'bigcommerce' ),
				self::ACTION_DISCONNECT => __( 'Disconnect', 'bigcommerce' ),
			] );
		}
	}

	public function render_other_channels() {
		$terms = $this->get_channel_terms( Channel::STATUS_DISCONNECTED );

		foreach ( $terms as $term ) {
			$this->render_channel_row( $term, Channel::STATUS_DISCONNECTED, [
				self::ACTION_CONNECT => __( 'Connect', 'bigcommerce' ),
			] );
		}

		echo '<div class="bigcommerce-channel bigcommerce-channel--new" data-js="bc-channel-row">';
		printf( '<p class="bigcommerce-channel-create"><a href="#" class="bigcommerce-channel-action bigcommerce-channel-action-create" data-js="bc-channel-show-action" data-action-type="create">%s</a></p>', esc_html( __( 'Create new channel', 'bigcommerce' ) ) );
		printf(
			'<p class="bigcommerce-new-channel-name" data-js="bc-channel-action" style="display: none;"><input type="text" class="regular-text code" value="" name="%s" data-js="bigcommerce-channel-action-input" /> <a href="#" class="bigcommerce-channel-action bigcommerce-cancel-new-channel" data-js="bc-channel-cancel-action"><i class="bc-icon bc-icon--settings icon-bc-cross"></i> %s</a><span class="description">%s</span></p>',
			esc_attr( self::NEW_NAME ),
			esc_html( __( 'Cancel', 'bigcommerce' ) ),
			esc_html( __( 'Press Save Changes to create this new channel.', 'bigcommerce' ) )
		);
		echo '</div>';
	}

	/**
	 * Retrieve all Channel taxonomy terms with the
	 * given connection status.
	 *
	 * @param string $status
	 *
	 * @return \WP_Term[]
	 */
	private function get_channel_terms( $status ) {
		$query = new \WP_Term_Query();

		return $query->query( [
			'taxonomy'   => Channel::NAME,
			'meta_query' => [
				[
					'key'   => Channel::STATUS,
					'value' => $status,
				],
			],
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
		] );
	}

	/**
	 * Render a single row for a Channel term
	 *
	 * @param \WP_Term $term    The Channel taxonomy term
	 * @param string   $status  The status of the term
	 * @param array    $actions The action type => label for each applicable action for this row
	 *
	 * @return void
	 */
	private function render_channel_row( \WP_Term $term, $status, array $actions ) {
		printf( '<div class="bigcommerce-channel bigcommerce-channel--%s" data-js="bc-channel-row">', esc_attr( $status ) );
		$action_links = [];
		foreach ( $actions as $key => $label ) {
			$action_links[] = sprintf(
				'<a href="%s" class="bigcommerce-channel-action bigcommerce-channel-action-%s" data-js="bc-channel-show-action" data-action-type="%2$s"><i class="bc-icon bc-icon--settings"></i> %s</a>',
				$this->get_channel_action_url( $key, $term ),
				esc_attr( $key ),
				esc_html( $label )
			);
		}
		printf(
			'<p class="bigcommerce-channel-name">%s %s</p>',
			esc_html( $term->name ),
			implode( ' ', $action_links )
		);
		if ( array_key_exists( 'rename', $actions ) ) {
			printf(
				'<p class="bigcommerce-channel-rename" data-js="bc-channel-action" style="display: none;"><input type="text" class="regular-text code" value="%s" name="%s[%s]" data-js="bigcommerce-channel-action-input" /> <a href="#" class="bigcommerce-channel-action bigcommerce-cancel-rename-channel" data-js="bc-channel-cancel-action" data-channel-name="%s"><i class="bc-icon bc-icon--settings icon-bc-cross"></i> %s</a><span class="description">%s</span></p>',
				esc_attr( $term->name ),
				esc_attr( self::CHANNEL_NAME ),
				esc_attr( $term->term_id ),
				esc_attr( $term->name ),
				esc_html( __( 'Cancel', 'bigcommerce' ) ),
				esc_html( __( 'Press Save Changes to update this channel name.', 'bigcommerce' ) )
			);
		}
		echo '</div>';
	}

	/**
	 * Build the URL to perform the given operation on the Channel term
	 *
	 * @param string   $action
	 * @param \WP_Term $term
	 *
	 * @return string
	 */
	private function get_channel_action_url( $action, \WP_Term $term ) {
		switch ( $action ) {
			case self::ACTION_CONNECT:
			case self::ACTION_DISCONNECT:
			case self::ACTION_PROMOTE:
				$url = add_query_arg( [
					'action'    => self::POST_ACTION,
					'term_id'   => $term->term_id,
					'operation' => $action,
				], admin_url( 'admin-post.php' ) );

				return wp_nonce_url( $url, $action );
			case self::ACTION_RENAME:
			default:
				return '#';
		}
	}

	/**
	 * Handle a request to perform an operation on an existing channel
	 *
	 * @param string $redirect URL to redirect to after handling the submission
	 *
	 * @return void
	 * @action admin_post_ . self::POST_ACTION
	 */
	public function handle_action_submission( $redirect ) {
		$submission = $this->validate_action_submission( $_REQUEST );
		if ( is_wp_error( $submission ) ) {
			wp_die( $submission, esc_html( __( 'Invalid request', 'bigcommerce' ) ), [ 'response' => 400 ] );
		}
		/** @var \WP_Term $term */
		$term            = $submission['term'];
		$previous_status = get_term_meta( $term->term_id, Channel::STATUS, true );
		if ( $previous_status === Channel::STATUS_PRIMARY ) {
			wp_die( esc_html( __( 'Unable to change status of primary channel. Promote another channel to the primary position before modifying.', 'bigcommerce' ) ) );
		}
		switch ( $submission['action'] ) {
			case self::ACTION_CONNECT:
				$this->connect_channel( $term );
				break;
			case self::ACTION_DISCONNECT:
				$this->disconnect_channel( $term );
				break;
			case self::ACTION_PROMOTE:
				$this->promote_channel( $term );
				break;
		}
		wp_safe_redirect( $redirect, 303 );
		exit();
	}

	/**
	 * Validate a submission of a channel operation request
	 *
	 * @param array $submission
	 *
	 * @return array|\WP_Error
	 */
	private function validate_action_submission( $submission ) {
		$submission = wp_parse_args( $submission, [
			'term_id'   => 0,
			'operation' => '',
			'_wpnonce'  => '',
		] );
		if ( ! wp_verify_nonce( $submission['_wpnonce'], $submission['operation'] ) ) {
			return new \WP_Error( 'invalid_nonce', __( 'Invalid request. Please try again.', 'bigcommerce' ) );
		}
		$term_id = absint( $submission['term_id'] );
		if ( empty( $term_id ) ) {
			return new \WP_Error( 'invalid_channel', __( 'Invalid request. Please try again.', 'bigcommerce' ) );
		}
		$term = get_term( $term_id, Channel::NAME );
		if ( is_wp_error( $term ) ) {
			return $term;
		}
		if ( empty( $term ) ) {
			return new \WP_Error( 'invalid_channel', __( 'Invalid request. Please try again.', 'bigcommerce' ) );
		}

		return [
			'term'   => $term,
			'action' => filter_var( $submission['operation'], FILTER_SANITIZE_STRING ),
		];
	}

	/**
	 * Mark a channel as connected
	 *
	 * @param \WP_Term $term
	 *
	 * @return void
	 */
	private function connect_channel( \WP_Term $term ) {
		update_term_meta( $term->term_id, Channel::STATUS, Channel::STATUS_CONNECTED );
		/**
		 * Triggered when the channel(s) connected to the site have changed
		 *
		 * @param \WP_Term $channel The Channel term updated
		 * @param string   $status  The status set on the channel
		 */
		do_action( 'bigcommerce/channel/connection_changed', $term, Channel::STATUS_CONNECTED );
	}

	/**
	 * Mark a channel as disconnected
	 *
	 * @param \WP_Term $term
	 *
	 * @return void
	 */
	private function disconnect_channel( \WP_Term $term ) {
		update_term_meta( $term->term_id, Channel::STATUS, Channel::STATUS_DISCONNECTED );
		/**
		 * Triggered when the channel(s) connected to the site have changed
		 *
		 * @param \WP_Term $channel The Channel term updated
		 * @param string   $status  The status set on the channel
		 */
		do_action( 'bigcommerce/channel/connection_changed', $term, Channel::STATUS_DISCONNECTED );
	}

	/**
	 * Promote a channel to the "Primary" status, demoting
	 * the current primary to the "Connected" status
	 *
	 * @param \WP_Term $term
	 *
	 * @return void
	 */
	public function promote_channel( \WP_Term $term ) {
		// demote the existing primary channel(s)
		$former_primary = get_terms( [
			'taxonomy'   => Channel::NAME,
			'meta_query' => [
				[
					'key'   => Channel::STATUS,
					'value' => Channel::STATUS_PRIMARY,
				],
			],
			'hide_empty' => false,
		] );
		/*
		 * There should only ever be a single primary channel. Use this opportunity
		 * to enforce that if multiple have somehow been set.
		 */
		foreach ( $former_primary as $former ) {
			update_term_meta( $former->term_id, Channel::STATUS, Channel::STATUS_CONNECTED );
		}

		// then promote the new one
		update_term_meta( $term->term_id, Channel::STATUS, Channel::STATUS_PRIMARY );
		$channel_id = get_term_meta( $term->term_id, Channel::CHANNEL_ID, true );
		update_option( self::CHANNEL_ID, $channel_id );

		/**
		 * Triggered when the channel(s) connected to the site have changed
		 *
		 * @param \WP_Term $channel The Channel term updated
		 * @param string   $status  The status set on the channel
		 */
		do_action( 'bigcommerce/channel/connection_changed', $term, Channel::STATUS_PRIMARY );
	}

	/**
	 * Save channel name changes to taxonomy terms, not to options
	 *
	 * @param array $value
	 *
	 * @return null
	 */
	public function save_channel_names( $value ) {
		if ( ! is_array( $value ) ) {
			return null;
		}
		foreach ( $value as $term_id => $channel_name ) {
			$channel_name = sanitize_text_field( $channel_name );
			if ( empty( $channel_name ) ) {
				continue;
			}
			$term = get_term( $term_id, Channel::NAME );
			if ( $term->name !== $channel_name ) {
				wp_update_term( $term->term_id, $term->taxonomy, [
					'name' => $channel_name,
				] );
			}
		}

		return null;
	}
}
