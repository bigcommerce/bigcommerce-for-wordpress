<?php


namespace BigCommerce\Import;

use BigCommerce\Exceptions\No_Task_Found_Exception;
use BigCommerce\Import\Processors\Term_Import;
use BigCommerce\Import\Processors\Term_Purge;
use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Import\Runner\Status;
use BigCommerce\Logging\Error_Log;

/**
 * Class Task_Manager
 *
 * Responsible for coordinating the sequence of import tasks
 */
class Task_Manager {

	/**
	 * @var Task_Definition[][]
	 */
	private $tasks = [];

	/**
	 * Register a new task
	 *
	 * @param Task_Definition $task
	 *
	 * @return void
	 */
	public function register( Task_Definition $task ) {
		$priority = $task->get_priority();

		$this->tasks[ $priority ][] = $task;
	}

	/**
	 * Remove a task from the registry
	 *
	 * @param Task_Definition $task
	 *
	 * @return void
	 */
	public function unregister( Task_Definition $task ) {
		$priority = $task->get_priority();
		if ( array_key_exists( $priority, $this->tasks ) ) {
			$this->tasks[ $priority ] = array_filter( $this->tasks[ $priority ], function ( $a ) use ( $task ) {
				return $a != $task; // Compare values (!=), not identify (!==)
			} );
			if ( empty( $this->tasks[ $priority ] ) ) {
				unset( $this->tasks[ $priority ] );
			}
		}
	}

	/**
	 * @param string $state The current state of the import
	 *
	 * @return Task_Definition
	 */
	public function get_task( $state ) {
		ksort( $this->tasks );
		$prior_complete = false;
		if ( empty( $state ) || $state === Status::NOT_STARTED ) {
			$prior_complete = true; // take the first task we find
		}

		foreach ( $this->tasks as $group ) {
			foreach ( $group as $task ) {
				if ( $prior_complete || in_array( $state, $task->get_in_progress_states() ) ) {
					return $task;
				} elseif ( $state === $task->get_completion_state() ) {
					$prior_complete = true;
				}
			}
		}

		throw new No_Task_Found_Exception( sprintf( __( 'No task found to handle state %s', 'bigcommerce' ), $state ) );
	}

	/**
	 * Run the next task in the queue
	 *
	 * @param string $state
	 *
	 * @return bool
	 */
	public function run_next( $state ) {
		try {
			$task     = $this->get_task( $state );
			$callback = $task->get_callback();
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Running import task', 'bigcommerce' ), [
				'state'       => $state,
				'description' => $task->get_description(),
			] );
			$callback( $state );

			return true;
		} catch ( No_Task_Found_Exception $e ) {
			do_action( 'bigcommerce/log', Error_Log::NOTICE, __( 'No handler found for current import state', 'bigcommerce' ), [
				'state'     => $state,
				'import'    => Import_Type::is_traditional_import() ? 'full' : 'headless',
			] );
			do_action( 'bigcommerce/log', Error_Log::DEBUG, $e->getTraceAsString(), [] );

			if ( ! Import_Type::is_traditional_import() ) {
				return false;
			}

			// Handle not found task loop for traditional import
			switch ( $state ) {
				case Status::PURGED_CATEGORIES:
					delete_option( Term_Purge::STATE_OPTION );
					$this->run_next( Status::PURGING_BRANDS );
					break;
				case Status::UPDATED_BRANDS:
					delete_option( Term_Import::STATE_OPTION );
					$this->run_next( Status::RESIZING_IMAGES );
					break;
				case Status::NOT_STARTED:
				case Status::COMPLETED:
					wp_unschedule_hook( Cron_Runner::START_CRON );
					wp_unschedule_hook( Cron_Runner::CONTINUE_CRON );
					$status = new Status();
					$status->set_status( Status::COMPLETED );
					$status->rotate_logs(); // must rotate _after_ status set to complete

					do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Import complete', 'bigcommerce' ), [] );
					break;
				default:
					return false;
			}
		}
	}

	/**
	 * Get the total count of registered tasks
	 *
	 * @return int
	 */
	public function task_count() {
		return array_sum( array_map( 'count', $this->tasks ) );
	}

	/**
	 * Get the count of tasks completed to get to the current state
	 *
	 * @param $state
	 *
	 * @return int
	 * @throws No_Task_Found_Exception
	 */
	public function completed_count( $state ) {
		ksort( $this->tasks );
		if ( empty( $state ) || $state === Status::NOT_STARTED ) {
			return 0;
		}

		$count = 0;

		foreach ( $this->tasks as $group ) {
			foreach ( $group as $task ) {
				if ( in_array( $state, $task->get_in_progress_states() ) ) {
					return $count;
				} elseif ( $state === $task->get_completion_state() ) {
					return $count + 1;
				}
				$count ++;
			}
		}

		throw new No_Task_Found_Exception( sprintf( __( 'No task found to handle state %s', 'bigcommerce' ), $state ) );
	}
}
