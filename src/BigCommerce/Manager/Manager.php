<?php

namespace BigCommerce\Manager;

use BigCommerce\Import\Runner\Cron_Runner;
use BigCommerce\Logging\Error_Log;
use BigCommerce\Schema\Queue_Table;

/**
 * @class Manager
 *        Responsible for tasks processing and scheduling recurring queue processor job
 */
class Manager {

	const CRON_PROCESSOR = 'bigcommerce_manager_processor';
	const FREQUENCY      = 'bigcommerce_task_processor_frequency';

	/**
	 * Process tasks queue
	 */
	public function run_tasks() {
		global $wpdb;

		$blog_table = $wpdb->prefix . Queue_Table::NAME;
		$sql        = $wpdb->prepare( "SELECT * FROM `$blog_table` WHERE status = %s", Queue_Table::STATUS_NEW );
		$tasks      = $wpdb->get_results( $sql );

		if ( empty( $tasks ) ||  is_wp_error( $tasks ) ) {
			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Task queue is empty', 'bigcommerce' ), [], 'manager' );

			return;
		}

		foreach ( $tasks as $task ) {
			if ( empty( $task->handler ) || ! class_exists( $task->handler ) ) {
				do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Task handler is empty or does not exist', 'bigcommerce' ), [
					'task' => $task
				], 'manager' );

				continue;
			}

			/** @var \BigCommerce\Manager\Task $task_class */
			$task_class = new $task->handler();
			$result     = $task_class->handle( $task->args );

			if ( ! $result ) {
				do_action( 'bigcommerce/log', Error_Log::ERROR, __( 'Could not handle task', 'bigcommerce' ), [
					'task' => $task
				], 'manager' );

				continue;
			}

			do_action( 'bigcommerce/log', Error_Log::INFO, __( 'Task finished', 'bigcommerce' ), [
				'task' => $task
			], 'manager' );
		}
	}

	/**
	 * Schedule new recurring event if it doesn't exist
	 */
	public function maybe_schedule_queue_processor() {
		if ( ! wp_next_scheduled( self::CRON_PROCESSOR ) && ! wp_next_scheduled( Cron_Runner::CONTINUE_CRON ) && ! wp_next_scheduled( Cron_Runner::START_CRON ) ) {
			wp_schedule_event( time(), self::FREQUENCY, self::CRON_PROCESSOR );
		}
	}

	/**
	 * Add new interval for task processor cron job
	 *
	 * @return array
	 */
	public function add_interval(): array {
		$cron_schedules[ self::FREQUENCY ] = [
			'interval' => 120,
			'display'  => __( 'Bigcommerce Task processor', 'tribe' ),
		];

		return $cron_schedules;
	}

}
