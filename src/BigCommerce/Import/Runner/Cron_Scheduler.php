<?php


namespace BigCommerce\Import\Runner;


use BigCommerce\Settings\Sections\Import;

class Cron_Scheduler {
	/**
	 * Schedule a cron to start the next import
	 *
	 * @return void
	 */
	public function schedule_next_import() {
		$frequency = get_option( Import::OPTION_FREQUENCY, Import::DEFAULT_FREQUENCY );
		if ( $frequency === Import::FREQUENCY_NEVER ) {
			wp_unschedule_hook( Cron_Runner::START_CRON );
			return;
		}

		$status   = new Status();
		$previous = $status->previous_status();

		if ( $previous[ 'status' ] == Status::NOT_STARTED ) {
			// no previous run, so start now
			wp_unschedule_hook( Cron_Runner::START_CRON );
			wp_schedule_single_event( time(), Cron_Runner::START_CRON );

			return;
		}

		$last = (int) $previous[ 'timestamp' ];
		
		switch ( $frequency ) {
			case Import::FREQUENCY_HOURLY:
				$offset = HOUR_IN_SECONDS;
				break;
			case Import::FREQUENCY_THIRTY:
				$offset = 30 * MINUTE_IN_SECONDS;
				break;
			case Import::FREQUENCY_FIVE:
				$offset = 5 * MINUTE_IN_SECONDS;
				break;
			case Import::FREQUENCY_WEEKLY:
				$offset = WEEK_IN_SECONDS;
				break;
			case Import::FREQUENCY_MONTHLY:
				$offset = MONTH_IN_SECONDS;
				break;
			case Import::FREQUENCY_DAILY:
			default:
				$offset = DAY_IN_SECONDS;
				break;
		}

		$scheduled = wp_next_scheduled( Cron_Runner::START_CRON );
		$next      = $last + $offset;
		if ( $scheduled && $scheduled > $next ) {
			wp_unschedule_hook( Cron_Runner::START_CRON );
			$scheduled = false;
		}
		if ( ! $scheduled ) {
			wp_schedule_single_event( $next, Cron_Runner::START_CRON );
		}
	}

	/**
	 * Schedule a cron to start the next phase of the current import
	 *
	 * @return void
	 */
	public function schedule_next_batch() {
		wp_schedule_single_event( time(), Cron_Runner::CONTINUE_CRON );
	}
}