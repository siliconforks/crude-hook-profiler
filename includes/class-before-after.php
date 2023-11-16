<?php

namespace siliconforks\crude_hook_profiler;

use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Before_After {
	public $hook;

	public $key;

	public $callback;

	public $stack_size = 0;

	public $elapsed_time = 0;

	public $start_time = NULL;

	public function __construct( $hook, $key, $callback ) {
		$this->hook = $hook;
		$this->key = $key;
		$this->callback = $callback;
	}

	public function before( $value = NULL ) {
		if ( $this->stack_size === 0 ) {
			$this->start_time = hrtime( TRUE );
		}
		++$this->stack_size;
		return $value;
	}

	public function after( $value = NULL ) {
		--$this->stack_size;
		if ( $this->stack_size === 0 ) {
			if ( $this->start_time === NULL ) {
				throw new Exception( 'Start time is NULL' );
			}
			$this->elapsed_time += hrtime( TRUE ) - $this->start_time;
			$this->start_time = NULL;
		}
		return $value;
	}
}
