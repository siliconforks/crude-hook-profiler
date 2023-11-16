<?php

namespace siliconforks\crude_hook_profiler;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hooks {
	public static $hooks_seen = [];

	public static $timings = [];
}
