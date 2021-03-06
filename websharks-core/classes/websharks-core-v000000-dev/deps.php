<?php
/**
 * Dependency Utilities.
 *
 * @note MUST remain PHP v5.2 compatible.
 *
 * Copyright: © 2012 (coded in the USA)
 * {@link http://www.websharks-inc.com WebSharks™}
 *
 * @author JasWSInc
 * @package WebSharks\Core
 * @since 120318
 */
# -----------------------------------------------------------------------------------------------------------------------------------------
# Only if WordPress® is loaded; and the WebSharks™ Core deps class does NOT exist yet.
# -----------------------------------------------------------------------------------------------------------------------------------------
if(!defined('WPINC'))
	exit('Do NOT access this file directly: '.basename(__FILE__));

if(!class_exists('deps_websharks_core_v000000_dev'))
	{
		# -----------------------------------------------------------------------------------------------------------------------------------
		# Load WebSharks™ Core stub class dependency (if NOT yet loaded).
		# -----------------------------------------------------------------------------------------------------------------------------------

		if(!class_exists('websharks_core_v000000_dev'))
			{
				$GLOBALS['autoload_websharks_core_v000000_dev'] = FALSE;
				require_once dirname(dirname(dirname(__FILE__))).'/stub.php';
			}
		# -----------------------------------------------------------------------------------------------------------------------------------
		# WebSharks™ Core deps class definition.
		# -----------------------------------------------------------------------------------------------------------------------------------
		/**
		 * Dependency Utilities.
		 *
		 * @note MUST remain PHP v5.2 compatible.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @see deps_x_websharks_core_v000000_dev
		 */
		final class deps_websharks_core_v000000_dev // Static properties/methods only please.
		{
			# --------------------------------------------------------------------------------------------------------------------------------
			# Protected properties.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * Initialized yet?
			 *
			 * @var boolean Initialized yet?
			 */
			protected static $initialized = FALSE;

			# --------------------------------------------------------------------------------------------------------------------------------
			# Initializer.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * Initializes WebSharks™ Core deps.
			 *
			 * @return boolean Returns the ``$initialized`` property w/ a TRUE value.
			 */
			public static function initialize()
				{
					if(self::$initialized)
						return TRUE; // Initialized already.
					/*
					 * Easier access for those who DON'T CARE about the version (PHP v5.3+ only).
					 */
					if(!class_exists('websharks_core__deps') && function_exists('class_alias') /* PHP v5.3+ only. */)
						class_alias(__CLASS__, 'websharks_core__deps');

					return (self::$initialized = TRUE);
				}

			# --------------------------------------------------------------------------------------------------------------------------------
			# Front-runner for extended dependency utilities.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * This is simply a front-runner for the extended dependency utilities provided by the WebSharks™ Core.
			 *    There is NO need to load the entire dependency scanner unless we really need to.
			 *
			 * @inheritdoc deps_x_websharks_core_v000000_dev::check()
			 * @see deps_x_websharks_core_v000000_dev::check()
			 */
			public static function check($plugin_name = '', $plugin_dir_names = '', $report_notices = TRUE, $report_warnings = TRUE,
			                             $check_last_ok = TRUE, $maybe_display_wp_admin_notices = TRUE)
				{
					if(!is_string($plugin_name) || !is_string($plugin_dir_names) || !is_bool($report_notices)
					   || !is_bool($report_warnings) || !is_bool($check_last_ok) || !is_bool($maybe_display_wp_admin_notices)
					) throw new exception(sprintf(websharks_core_v000000_dev::i18n('Invalid arguments: `%1$s`'), print_r(func_get_args(), TRUE)));

					if(apply_filters('websharks_core__deps__check_disable', FALSE))
						return TRUE; // Return now (DISABLED by a filter).

					$php_version = PHP_VERSION; // Installed PHP version.
					global $wp_version; // Global made available by WordPress®.

					if($check_last_ok // Has a full scan succeeded in the past?
					   && is_array($last_ok = get_option('websharks_core__deps__last_ok'))
					   && isset($last_ok['websharks_core_v000000_dev'], $last_ok['time'], $last_ok['php_version'], $last_ok['wp_version'])
					   && $last_ok['time'] >= strtotime('-7 days') && $last_ok['php_version'] === $php_version && $last_ok['wp_version'] === $wp_version
					) return TRUE; // Return TRUE. A re-scan is NOT necessary; everything is still OK.

					if(!class_exists('deps_x_websharks_core_v000000_dev'))
						require_once dirname(__FILE__).'/deps-x.php';

					$x             = new deps_x_websharks_core_v000000_dev();
					$check_last_ok = FALSE; // We just checked this above. No reason to check again.
					return $x->check($plugin_name, $plugin_dir_names, $report_notices, $report_warnings, $check_last_ok, $maybe_display_wp_admin_notices);
				}

			# --------------------------------------------------------------------------------------------------------------------------------
			# Routines related to activation/deactivation.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * Removes data/procedures associated with this class.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully uninstalled, else FALSE.
			 *
			 * @see deps_x_websharks_core_v000000_dev::deactivation_uninstall()
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 */
			public static function deactivation_uninstall($confirmation = FALSE)
				{
					if(!is_bool($confirmation))
						throw new exception( // Fail here; detected invalid arguments.
							sprintf(websharks_core_v000000_dev::i18n('Invalid arguments: `%1$s`'),
							        print_r(func_get_args(), TRUE))
						);
					if(!$confirmation)
						return FALSE; // Added security.

					delete_option('websharks_core__deps__last_ok');
					delete_option('websharks_core__deps__notice__dismissals');

					if(!class_exists('deps_x_websharks_core_v000000_dev'))
						require_once dirname(__FILE__).'/deps-x.php';

					$x = new deps_x_websharks_core_v000000_dev();

					return $x->deactivation_uninstall(TRUE);
				}
		}

		# -----------------------------------------------------------------------------------------------------------------------------------
		# Initialize the WebSharks™ Core deps class.
		# -----------------------------------------------------------------------------------------------------------------------------------

		deps_websharks_core_v000000_dev::initialize(); // Also creates class alias.
	}