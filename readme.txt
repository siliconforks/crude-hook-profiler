=== Crude Hook Profiler ===
Contributors: siliconforks
Tags: performance
Requires at least: 6.3
Tested up to: 6.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin simply measures the time taken by all hook functions and logs the slowest ones to the error log.

== Description ==

To use this plugin, you need to define a constant in your wp-config.php file:

define( 'CRUDE_HOOK_PROFILER_COOKIE', 'YOUR_SECRET_PASSWORD' );

Change 'YOUR_SECRET_PASSWORD' to a unique value for your site.
It should include letters and numbers only.

Now you can make requests to your site using this cookie:

curl --cookie crude_hook_profiler=YOUR_SECRET_PASSWORD http://example.test/wordpress/

This will log the slowest hook functions to your site's error log.
