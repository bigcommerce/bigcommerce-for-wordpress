<?php


namespace BigCommerce\Import;


class Task_Definition {
	/**
	 * @var callable
	 */
	private $callback;

	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var string
	 */
	private $completion_state;
	/**
	 * @var array
	 */
	private $in_progress_states;
	/**
	 * @var string
	 */
	private $description;

	/**
	 * Task_Definition constructor.
	 *
	 * @param callable $callback           The callback to run this task
	 * @param int      $priority           The order in which the task should run. Lower values run before higher values.
	 * @param string   $completion_state   The state at which this task is considered complete
	 * @param array    $in_progress_states An array of optional interim states where the task should keep running.
	 * @param string   $description        A user-friendly description of the task
	 */
	public function __construct( $callback, $priority, $completion_state, $in_progress_states = [], $description = '' ) {
		if ( ! is_callable( $callback ) ) {
			throw new \InvalidArgumentException( __( 'Invalid callback.', 'bigcommerce' ) );
		}
		if ( empty( $completion_state ) ) {
			throw new \InvalidArgumentException( __( 'Completion state must not be empty.', 'bigcommerce' ) );
		}
		$this->callback           = $callback;
		$this->priority           = (int) $priority;
		$this->completion_state   = (string) $completion_state;
		$this->in_progress_states = array_filter( $in_progress_states );
		$this->description        = $description;
	}

	public function get_callback() {
		return $this->callback;
	}

	public function get_priority() {
		return $this->priority;
	}

	public function get_completion_state() {
		return $this->completion_state;
	}

	public function get_in_progress_states() {
		return $this->in_progress_states;
	}

	public function get_description() {
		return $this->description;
	}
}