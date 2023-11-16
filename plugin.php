<?php
/**
 * Plugin Name: Crude Hook Profiler
 * Description: This plugin simply measures the time taken by all hook functions and logs the slowest ones to the error log.
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Requires at least: 6.3
 * Requires PHP: 7.4
 * Update URI: false
 */

/*
Copyright (C) 2023  siliconforks

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'CRUDE_HOOK_PROFILER_COOKIE' ) && isset( $_COOKIE['crude_hook_profiler'] ) && hash_equals( CRUDE_HOOK_PROFILER_COOKIE, wp_unslash( $_COOKIE['crude_hook_profiler'] ) ) ) {
	require __DIR__ . '/includes/profiler.php';
}
