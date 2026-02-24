<?php
/**
 * Test for pull request 10.
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2022 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Documentor
 */

/**
 * Test function for prefix 1.
 *
 * @param string $first_param This is the first argument.
 * @param string $second_param This is the second argument.
 * @return string
 */
function test_issue_10_prefix_1( $first_param, $second_param ) {
	/**
	 * Prefix 1 description.
	 *
	 * @param string $first_param_prefix_1 This is the first argument. Change up description!
	 * @param object $second_param_prefix_1 This is the second argument for prefix 1
	 */
	return apply_filters( 'prefix_1_filter_name', $first_param_test, $second_param_test );
}

/**
 * Test function for prefix 2.
 *
 * @param string $first_param This is the first argument.
 * @param string $second_param This is the second argument.
 * @return string
 */
function test_issue_10_prefix_2( $first_param, $second_param ) {
	/**
	 * Prefix 2 description.
	 *
	 * @param array $first_param_prefix_2 This is the first argument for prefix 2
	 * @param Exampletype $second_param_prefix_2 This is the second argument for prefix 2
	 */
	return apply_filters( 'prefix_2_filter_name', $first_param, $second_param );
}

/**
 * Test function for prefix 3.
 *
 * @param string $first_param This is the first argument.
 * @param string $second_param This is the second argument.
 * @return string
 */
function test_issue_10_prefix_3( $first_param, $second_param ) {
	/**
	 * Prefix 3 description.
	 *
	 * @param integer $first_param_prefix_3 This is the first argument for prefix 3
	 * @param boolean $second_param_prefix_3 This is the second argument for prefix 3.
	 */
	return apply_filters( 'prefix_3_filter_name', $first_param, $second_param );
}

/**
 * Test function for prefix 4
 */

function test_issue_10_prefix_4( $first_param, $second_param ) {

	return do_action( 'prefix_4_filter_nameee', $first_param, $second_param, $third_param );
}

/**
 * additional Prefix 4 description.
 *
 * @param int $tenth_param_prefix_4 This is the 7th argument for prefix 4
 * @param int $ele_param_prefix_4 This is the 8th argument for prefix 4
 * @param int $twelvth_param_prefix_4 This is the 9th argument for prefix 4!!!
 */
function test_issue_10_prefix_5( $first_param, $second_param ) {
	/**
	 * Prefix 4 description...
	 *
	 * @param string $first_param_prefix_4 This is the 1st argument for prefix 4
	 * @param object $second_param_prefix_4 This is the 2nd argument for prefix 4
	 * @param string $third_param_prefix_4 This is the 3rd argument for prefix 4...
	 * @param string $fourth_param_prefix_4 This is the 4th argument for prefix 4
	 * @param boolean $fifth_param_prefix_4 This is the 5th argument for prefix 4...
	 * @param string $six_param_prefix_44 This is the 6th argument for prefix 4........
	 */
	return do_action( 'prefix_4_filter_nameee', $first_parammm, $second_parammm, $third_parammm );
}
