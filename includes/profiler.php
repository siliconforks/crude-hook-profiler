<?php

namespace siliconforks\crude_hook_profiler;

use Closure;
use ReflectionFunction;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require __DIR__ . '/class-before-after.php';
require __DIR__ . '/class-hooks.php';

function get_function_name( $function, $key ) {  // phpcs:ignore Universal.NamingConventions.NoReservedKeywordParameterNames.functionFound
	if ( is_string( $function ) ) {
		$function_name = $function;
	}
	elseif ( is_array( $function ) && isset( $function[0], $function[1] ) ) {
		$object_or_class = $function[0];
		$method = $function[1];
		if ( is_string( $method ) ) {
			if ( is_string( $object_or_class ) ) {
				$class_name = $object_or_class;
			}
			elseif ( is_object( $object_or_class ) ) {
				$class_name = get_class( $object_or_class );
			}
			else {
				// ???
				$class_name = gettype( $object_or_class );
			}
			$function_name = $class_name . '::' . $method;
		}
		else {
			$function_name = $key;
		}
	}
	elseif ( is_object( $function ) ) {
		if ( $function instanceof Closure ) {
			$reflection_function = new ReflectionFunction( $function );
			$function_name = 'closure at ' . $reflection_function->getFileName() . ':' . $reflection_function->getStartLine();
		}
		else {
			$function_name = 'instance of ' . get_class( $function );
		}
	}
	else {
		$function_name = $key;
	}
	return $function_name;
}

function shutdown() {
	usort(
		Hooks::$timings,
		static function ( $a, $b ) {
			return $b->elapsed_time <=> $a->elapsed_time;
		}
	);

	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$request_uri = wp_unslash( $_SERVER['REQUEST_URI'] );
	}
	else {
		$request_uri = '???';
	}

	// TODO: need a way to configure this.
	$num_results = 10;

	$num_timings = count( Hooks::$timings );
	for ( $i = 0; $i < $num_timings && $i < $num_results; ++$i ) {
		$before_after = Hooks::$timings[ $i ];
		$elapsed_time = $before_after->elapsed_time;
		$hook = $before_after->hook;
		$key = $before_after->callback;
		$callback = $before_after->callback;
		$function = $callback['function'];
		$function_name = get_function_name( $function, $key );

		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $request_uri . ' SLOW HOOK #' . ( $i + 1 ) . ': ' . $hook . ': ' . $function_name . ' (elapsed time: ' . sprintf( '%.3F', $elapsed_time / 1e9 ) . ' seconds)' );
	}
}

function all() {
	$current_hook = current_filter();

	if ( ! isset( Hooks::$hooks_seen[ $current_hook ] ) ) {
		Hooks::$hooks_seen[ $current_hook ] = TRUE;

		if ( isset( $GLOBALS['wp_filter'][ $current_hook ] ) ) {
			$priorities = array_keys( $GLOBALS['wp_filter'][ $current_hook ]->callbacks );
			foreach ( $priorities as $priority ) {
				$new_callbacks = [];
				foreach ( $GLOBALS['wp_filter'][ $current_hook ]->callbacks[ $priority ] as $key => $callback ) {
					$before_after = new Before_After( $current_hook, $key, $callback );
					$before = [ $before_after, 'before' ];
					$after = [ $before_after, 'after' ];
					$before_key = _wp_filter_build_unique_id( $current_hook, $before, $priority );
					$after_key = _wp_filter_build_unique_id( $current_hook, $after, $priority );
					$new_callbacks[ $before_key ] = [
						'function' => $before,
						'accepted_args' => 1,
					];
					$new_callbacks[ $key ] = $callback;
					$new_callbacks[ $after_key ] = [
						'function' => $after,
						'accepted_args' => 1,
					];
					Hooks::$timings[] = $before_after;
				}
				$GLOBALS['wp_filter'][ $current_hook ]->callbacks[ $priority ] = $new_callbacks;
			}
		}
	}

	if ( $current_hook === 'shutdown' ) {
		add_filter( 'shutdown', __NAMESPACE__ . '\shutdown', PHP_INT_MAX, 0 );
	}
}

add_action( 'all', __NAMESPACE__ . '\all', 10, 0 );
