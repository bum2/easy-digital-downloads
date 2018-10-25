<?php
/**
 * Base Custom Table Class.
 *
 * @package     EDD
 * @subpackage  Database
 * @copyright   Copyright (c) 2018, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */
namespace EDD\Database;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * The base class that all other database base classes extend.
 *
 * This class attempts to provide some universal immutability to all other
 * database interfaces, starting with a magic getter, but likely expanding into
 * a magic call handler and others.
 *
 * @since 3.0
 */
class Base {

	/** Public ****************************************************************/

	/**
	 * Magic isset'ter for immutability.
	 *
	 * @since 3.0
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __isset( $key = '' ) {

		// No more uppercase ID properties ever
		if ( 'ID' === $key ) {
			$key = 'id';
		}

		// Class method to try and call
		$method = "get_{$key}";

		// Return property if exists
		if ( method_exists( $this, $method ) ) {
			return true;

		// Return get method results if exists
		} elseif ( property_exists( $this, $key ) ) {
			return true;
		}

		// Return false if not exists
		return false;
	}

	/**
	 * Magic getter for immutability.
	 *
	 * @since 3.0
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get( $key = '' ) {

		// No more uppercase ID properties ever
		if ( 'ID' === $key ) {
			$key = 'id';
		}

		// Class method to try and call
		$method = "get_{$key}";

		// Return property if exists
		if ( method_exists( $this, $method ) ) {
			return call_user_func( array( $this, $method ) );

		// Return get method results if exists
		} elseif ( property_exists( $this, $key ) ) {
			return $this->{$key};
		}

		// Return null if not exists
		return null;
	}

	/**
	 * Converts the given object to an array.
	 *
	 * @since 3.0
	 *
	 * @return array Array version of the given object.
	 */
	public function to_array() {
		return get_object_vars( $this );
	}

	/** Protected *************************************************************/

	/**
	 * Set class variables from arguments.
	 *
	 * @since 3.0
	 * @param array $args
	 */
	protected function set_vars( $args = array() ) {

		// Bail if empty or not an array
		if ( empty( $args ) ) {
			return;
		}

		// Cast to an array
		if ( ! is_array( $args ) ) {
			$args = (array) $args;
		}

		// Set all properties
		foreach ( $args as $key => $value ) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Return the global database interface.
	 *
	 * See: https://core.trac.wordpress.org/ticket/31556
	 *
	 * @since 3.0
	 *
	 * @return object Database interface, or False if not set
	 */
	protected function get_db() {

		// Default database return value (might change)
		$retval = false;

		// Look for the WordPress global database interface
		if ( isset( $GLOBALS['wpdb'] ) ) {
			$retval = $GLOBALS['wpdb'];
		}

		/*
		 * Developer note:
		 *
		 * It should be impossible for a database table to be interacted with
		 * before the primary database interface it is setup.
		 *
		 * However, because applications are complicated, it is unsafe to assume
		 * anything, so this silently returns false instead of halting everything.
		 *
		 * If you are here because this method is returning false for you, that
		 * means the database table is being invoked too early in the lifecycle
		 * of the application. In WordPress, that means before the $wpdb global
		 * is created; in other environments, you will need to adjust accordingly.
		 */

		// Return the database interface
		return $retval;
	}
}
