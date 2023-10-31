<?php


namespace BigCommerce\Post_Types\Sync_Log;

use BigCommerce\Import\Runner\Status;
use BigCommerce\Post_Types\Product\Product;
use BigCommerce\Settings\Import_Status;
use BigCommerce\Taxonomies\Channel\Channel;
use BigCommerce\Taxonomies\Channel\Connections;

class Sync_Log {

	const NAME = 'bigcommerce_sync_log';

	const META_STATUS          = 'status';
	const META_EVENT           = 'event';
	const META_ERRORS          = 'errors';
	const META_SUMMARY         = 'summary';
	const META_DURATION        = 'duration';
	const MULTICHANNEL_SUMMARY = 'multichannel_summary';
	// We need to know if multichannel was active during this log session.
	const MULTICHANNEL_ACTIVE = 'multichannel_active';

	// Track the sync_id so we don't have to get_posts multiple times.
	private $sync_id = 0;

	/**
	 * Create a draft sync log
	 *
	 * @return void
	 *
	 * @action bigcommerce/import/start
	 */
	public function create_sync() {
		$this->remove_old_syncs();

		$post_id = wp_insert_post( [
			'post_title'  => __( 'Sync in progress', 'bigcommerce' ),
			'post_type'   => self::NAME,
			'post_status' => 'draft',
		] );
		update_post_meta( $post_id, self::META_ERRORS, [] );
		update_post_meta( $post_id, self::META_SUMMARY, [
			'count_before' => wp_count_posts( Product::NAME )->publish,
		] );

		// Gather extra data for multichannel
		if ( Channel::multichannel_enabled() ) {
			update_post_meta( $post_id, self::MULTICHANNEL_ACTIVE, true );
			$connections = new Connections();

			// Get the existing meta
			$multichannel_summary = get_post_meta( $post_id, self::MULTICHANNEL_SUMMARY, true );
			$multichannel_summary = is_array( $multichannel_summary ) ? $multichannel_summary : [];

			foreach ( $connections->active() as $channel ) {
				$multichannel_summary[ $channel->term_id ] = [
					'count_before' => $channel->count,
				];
			}

			// And update the meta
			update_post_meta(
				$post_id,
				self::MULTICHANNEL_SUMMARY,
				$multichannel_summary
			);
		}
	}

	/**
	 * Called on import complete
	 *
	 * @param array $log The current log
	 *
	 * @return void
	 *
	 * @action bigcommerce/import/logs/rotate
	 */
	public function complete_sync( $log ) {
		$post_id = $this->get_current_sync_id();

		if ( empty( $post_id ) ) {
			return;
		}

		$start_time = key( $log ); // get first key
		$status     = end( $log ); // last value
		$end_time   = key( $log ); // last key

		$time = floatval( $end_time ) - floatval( $start_time );
		$time = date( 'H:i:s', $time );

		wp_update_post( [
			'ID'           => $post_id,
			'post_title'   => sprintf( __( 'Sync %s', 'bigcommerce' ), date( 'Y-m-d h:i:s' ) ),
			'post_type'    => self::NAME,
			'post_status'  => 'publish',
			'post_content' => wp_json_encode( $log ),
		] );

		$summary = get_post_meta( $post_id, self::META_SUMMARY, true );

		if ( empty( $summary ) ) {
			$summary = [];
		}

		$summary['total']       = (int) get_option( Import_Status::IMPORT_TOTAL_PRODUCTS, 0 );
		$summary['count_after'] = wp_count_posts( Product::NAME )->publish;

		update_post_meta( $post_id, self::META_EVENT, __( 'Product Sync', 'bigcommerce' ) );
		update_post_meta( $post_id, self::META_DURATION, $time );
		update_post_meta( $post_id, self::META_STATUS, $status );
		update_post_meta( $post_id, self::META_SUMMARY, $summary );

		if ( Channel::multichannel_enabled() ) {
			// Get the existing meta
			$multichannel_summary = get_post_meta( $post_id, self::MULTICHANNEL_SUMMARY, true );
			$multichannel_summary = is_array( $multichannel_summary ) ? $multichannel_summary : [];

			foreach ( $multichannel_summary as $term_id => $summary ) {
				$summary['count_after']  = get_term( $term_id )->count;
				$summary['channel_name'] = get_term( $term_id )->name;

				$multichannel_summary[ $term_id ] = $summary;
			}

			// And update the meta
			update_post_meta(
				$post_id,
				self::MULTICHANNEL_SUMMARY,
				$multichannel_summary
			);
		}

		$this->remove_old_syncs( 'publish', 10 );
	}

	/**
	 * Store errors
	 *
	 * @return void
	 *
	 * @action bigcommerce/import/error
	 */
	public function log_error( $error ) {
		$post_id = $this->get_current_sync_id();

		if ( empty( $post_id ) ) {
			return;
		}

		$errors = get_post_meta( $post_id, self::META_ERRORS, true );

		if ( ! is_array ($errors) || empty( $errors ) ) {
			$errors = [];
		}

		$errors[] = $error;

		update_post_meta( $post_id, self::META_ERRORS, $errors );
	}

	/**
	 * Add sync logs to Diagnostics panel
	 *
	 * @param array $data
	 *
	 * @return array
	 *
	 * @filter bigcommerce/diagnostics
	 */
	public function diagnostic_data( $data ) {
		$sync_logs = get_posts( [
			'post_type'      => self::NAME,
			'post_status'    => 'publish',
			'posts_per_page' => 10,
		] );

		$sync_log_data = [];
		foreach ( $sync_logs as $index => $sync_log ) {
			$status = $sync_log->{self::META_STATUS};
			$errors = $sync_log->{self::META_ERRORS};

			$sync_log_data[] = sprintf(
				'<tr rowspan="6">
					<td>%d</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
					<td>%s</td>
				</tr>',
				$index + 1,
				ucfirst( $status ),
				$sync_log->{self::MULTICHANNEL_ACTIVE}
					? $this->get_multichannel_summary_formatted( $sync_log )
					: $this->get_summary_formatted( $sync_log ),
				is_array( $errors ) && count( $errors ) ? implode( '<br>', $errors ) : __( 'None', 'bigcommerce' ),
				$sync_log->{self::META_DURATION},
				$this->get_post_date_formatted( $sync_log )
			);
		}

		$sync_log_data = sprintf(
			'<style>
				.bc-product-sync-data-table tr:nth-child(odd) {
                    background-color: #f2f2f2;
				}
			</style>
			<table class="bc-product-sync-data-table">
				<tr>
					<th>#</th>
					<th>%s</th>
					<th>%s</th>
					<th>%s</th>
					<th>%s</th>
					<th>%s (%s)</th>
				</tr>
				%s
			</table>',
			__( 'Status', 'bigcommerce' ),
			__( 'Summary', 'bigcommerce' ),
			__( 'Errors', 'bigcommerce' ),
			__( 'Duration', 'bigcommerce' ),
			__( 'Date/Time', 'bigcommerce' ),
			$this->get_timezone_string(),
			implode( '', $sync_log_data )
		);

		$data[] = [
			'label' => __( 'Product Sync Log', 'bigcommerce' ),
			'key'   => 'sync_log',
			'value' => [
				[
					'label' => '',
					'key'   => 'sync_logs',
					'value' => $sync_log_data,
				],
			],
		];

		return $data;
	}

	/**
	 * Get formatted post date
	 *
	 * @param \WP_Post $post
	 *
	 * @return string
	 */
	private function get_post_date_formatted( $post ) {
		$date_format = sprintf(
			'%s %s',
			get_option( 'date_format', 'Y-m-d' ),
			get_option( 'time_format', 'H:i' )
		);

		return get_the_date( $date_format, $post );
	}

	/**
	 * Get the timezone string
	 *
	 * @return string
	 */
	private function get_timezone_string() {
		$timezone = wp_timezone_string();
		if ( ! empty( $timezone[0] ) && $timezone[0] === '+' ) {
			$timezone = 'UTC' . $timezone;
		}

		return $timezone;
	}

	/**
	 * Get the sync ID
	 *
	 * @return int
	 */
	private function get_current_sync_id() {
		// Track the sync id so we can write the error after post status update.
		if ( $this->sync_id ) {
			return $this->sync_id;
		}

		$draft = get_posts( [
			'post_type'      => self::NAME,
			'post_status'    => 'draft',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		] );

		$this->sync_id = reset( $draft );

		return $this->sync_id;
	}

	/**
	 * Remove old sync logs
	 *
	 * @return void
	 */
	private function remove_old_syncs( $status = 'draft', $keep = 0 ) {
		$sync_logs = get_posts( [
			'post_type'      => self::NAME,
			'post_status'    => $status,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		] );

		if ( count( $sync_logs ) <= $keep ) {
			return;
		}

		foreach ( $sync_logs as $index => $post_id ) {
			if ( $index < $keep ) {
				continue;
			}
			wp_delete_post( $post_id, true );
		}
	}

	/**
	 * Get formatted summary
	 *
	 * @param \WP_Post $sync_log
	 *
	 * @return string
	 */
	private function get_summary_formatted( $sync_log ) {
		$status = $sync_log->{self::META_STATUS};

		if ( $status === Status::ABORTED ) {
			return '';
		}

		$summary = $sync_log->{self::META_SUMMARY};

		if ( empty( $summary['total'] ) ) {
			return '';
		}

		$total = $summary['total'];

		$out = [
			sprintf( __( '%d Products Synced', 'bigcommerce' ), $total ),
		];

		$count_before = $summary['count_before'] ?? 0;
		$count_after  = $summary['count_after'] ?? 0;
		$new          = $count_after - $count_before;

		if ( $new >= 0 && $new < $total ) {
			$out[] = sprintf( __( '%d Added', 'bigcommerce' ), $new );

			// Not accounting for ignored products
			$out[] = sprintf( __( '%d Updated', 'bigcommerce' ), $total - $new );
		}

		$out = array_map( function ( $line ) {
			return sprintf( '<span class="bc-sync-summary-meta">%s</span><br />', $line );
		}, $out );

		return implode( '', $out );
	}

	/**
	 * Get formatted summary
	 *
	 * @param \WP_Post $sync_log
	 *
	 * @return string
	 */
	private function get_multichannel_summary_formatted( $sync_log ) {
		$status = $sync_log->{self::META_STATUS};

		if ( $status === Status::ABORTED ) {
			return '';
		}

		$multichannel_summary = $sync_log->{self::MULTICHANNEL_SUMMARY};

		$summary = [];

		foreach ( $multichannel_summary as $channel ) {
			$count_before = $channel['count_before'] ?? 0;
			$count_after  = $channel['count_after'] ?? 0;
			$new          = $count_after - $count_before;

			$summary[] = sprintf( __( '<br /><b>%s</b>', 'bigcommerce' ), $channel['channel_name'] );
			$summary[] = sprintf( __( '%d Synced', 'bigcommerce' ), $channel['count_after'] );
			if ( $new > 0  ) {
				$summary[] = sprintf( __( '%d Added', 'bigcommerce' ), $new );
			}

			$summary = array_map( function ( $line ) {
				return sprintf( '<span class="bc-sync-summary-meta">%s</span>', $line );
			}, $summary );
		}

		return implode( '<br />', $summary );
	}
}
