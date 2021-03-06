<?php
/**
 * Exception Handler.
 *
 * Copyright: © 2012 (coded in the USA)
 * {@link http://www.websharks-inc.com WebSharks™}
 *
 * @author JasWSInc
 * @package WebSharks\Core
 * @since 120318
 */
# -----------------------------------------------------------------------------------------------------------------------------------------
# WebSharks™ Core exception handler (only if it does NOT exist yet). Handles all PHP exceptions.
# -----------------------------------------------------------------------------------------------------------------------------------------
namespace websharks_core_v000000_dev
	{
		if(!defined('WPINC'))
			exit('Do NOT access this file directly: '.basename(__FILE__));

		if(!class_exists('\\'.__NAMESPACE__.'\\exception_handler'))
			{
				# -----------------------------------------------------------------------------------------------------------------------------
				# WebSharks™ Core stub class (and an internal/namespaced alias).
				# -----------------------------------------------------------------------------------------------------------------------------

				if(!class_exists('\\'.__NAMESPACE__))
					{
						$GLOBALS['autoload_'.__NAMESPACE__] = FALSE;
						require_once dirname(dirname(dirname(__FILE__))).'/stub.php';
					}
				if(!class_exists('\\'.__NAMESPACE__.'\\stub'))
					class_alias('\\'.__NAMESPACE__, __NAMESPACE__.'\\stub');

				# -----------------------------------------------------------------------------------------------------------------------------
				# WebSharks™ Core exception handler definition.
				# -----------------------------------------------------------------------------------------------------------------------------
				/**
				 * Exception Handler.
				 *
				 * @package WebSharks\Core
				 * @since 120318
				 *
				 * @note This should NOT rely directly or indirectly on any other core class objects.
				 *    Any static properties/methods in the WebSharks™ Core stub will be fine to use though.
				 *    ~ This class walks a FINE LINE on this point. It DOES use core classes (when/if possible).
				 *
				 * @note This WebSharks™ Core exception handler can be disabled with a WordPress® filter.
				 *    ``add_filter('websharks_core__exception_handler__disable', '__return_true');``
				 */
				final class exception_handler // Static properties/methods only please.
				{
					# --------------------------------------------------------------------------------------------------------------------------
					# Protected/static properties (some are defined by the initializer).
					# --------------------------------------------------------------------------------------------------------------------------

					/**
					 * Initialized yet?
					 *
					 * @var boolean Initialized yet?
					 *
					 * @by-initializer Set by initializer.
					 */
					protected static $initialized = FALSE;

					/**
					 * Previous exception handler.
					 *
					 * @var string|array|null Previous exception handler.
					 *
					 * @by-initializer Set by initializer.
					 */
					protected static $previous_handler; // Defaults to a NULL value.

					# --------------------------------------------------------------------------------------------------------------------------
					# Public properties (so templates can use them).
					# --------------------------------------------------------------------------------------------------------------------------

					/**
					 * Exception being handled by this class.
					 *
					 * @var exception|\exception Exception class.
					 *
					 * @by-handler Set dynamically by handler.
					 */
					public static $exception; // Defaults to a NULL value.

					/**
					 * Plugin/framework instance.
					 *
					 * @var framework Plugin/framework instance.
					 *
					 * @by-handler Set dynamically by handler.
					 */
					public static $plugin; // Defaults to a NULL value.

					# --------------------------------------------------------------------------------------------------------------------------
					# Initializer.
					# --------------------------------------------------------------------------------------------------------------------------

					/**
					 * Initializes properties.
					 *
					 * @return boolean Returns the ``$initialized`` property w/ a TRUE value.
					 *
					 * @note Sets some class properties & registers exception handler.
					 */
					public static function initialize()
						{
							if(static::$initialized)
								return TRUE; // Initialized already.

							if(!apply_filters(stub::$core_ns_stub.'__exception_handler__disable', FALSE))
								static::$previous_handler = set_exception_handler('\\'.__CLASS__.'::handle');

							return (static::$initialized = TRUE);
						}

					# -----------------------------------------------------------------------------------------------------------------------------
					# WebSharks™ Core PHP exception handler.
					# -----------------------------------------------------------------------------------------------------------------------------

					/**
					 * Handles all PHP exceptions.
					 *
					 * @param exception|\exception $exception An exception.
					 *
					 * @note Exceptions handled successfully by this routine will NOT be logged by PHP as errors.
					 *    As the exception handler, we will need to log and/or display anything that needs to be recorded here.
					 *    The PHP interpreter simply terminates script execution whenever an exception occurs (nothing more).
					 *
					 * @note If an exception is thrown while handling an exception; PHP will revert to it's default exception handler.
					 *    This will result in a fatal error that may get logged by PHP itself (depending on `error_reporting` and `error_log`).
					 *
					 * @throws exception|\exception If we are unable to handle the exception (i.e. the WebSharks™ Core is not even available yet),
					 *    this handler will simply re-throw the exception (forcing a fatal error); as just described in the previous note.
					 *
					 * @note The display of exception messages is NOT dependent upon `display_errors`; nor do we consider that setting here.
					 *    However, we do tighten security within the `exception.php` template file; hiding most details by default; and displaying all details
					 *    only if the current user is a Super Administrator in WordPress; or if `WP_DEBUG_DISPLAY` mode has been enabled on this site.
					 *
					 * @note If there was another exception handler active on the site; and this exception is NOT for
					 *    a plugin under this version of the WebSharks™ Core; we simply hand the exception back to the previous handler.
					 *    In the case of multiple versions of the WebSharks™ Core across various plugins; this allows us to work up the chain
					 *    of previous handlers until we find the right version of the WebSharks™ Core; assuming each version
					 *    of the WebSharks™ Core handles things this way too (which is to be expected).
					 *
					 * @see http://php.net/manual/en/function.set-exception-handler.php
					 */
					public static function handle(\exception $exception)
						{
							try // We'll re-throw as an \exception if there are any issues here.
								{
									static::$exception = $exception; // Reference.

									if(static::$exception instanceof exception)
										{
											static::$plugin = static::$exception->plugin;
											static::handle_plugin_exception();
											return; // We're done here.
										}
									// Else this is some other type of exception.

									if(static::$previous_handler && is_callable(static::$previous_handler))
										{
											call_user_func(static::$previous_handler, static::$exception);
											return; // We're done here.
										}
									// There is NO other handler available (deal w/ it here; if possible).

									if(is_callable('\\'.stub::$core_ns.'\\core'))
										{
											static::$plugin = core();
											static::handle_plugin_exception();
											return; // We're done here.
										}
									throw static::$exception; // Re-throw (forcing a fatal error).
								}
							catch(\exception $_exception) // Rethrow a standard exception class.
								{
									throw new \exception(
										sprintf(stub::i18n('Failed to handle exception code: `%1$s` with message: `%2$s`.'), $exception->getCode(), $exception->getMessage()).
										sprintf(stub::i18n(' Failure caused by exception code: `%1$s` with message: `%2$s`.'), $_exception->getCode(), $_exception->getMessage()), 20, $_exception
									);
								}
						}

					# --------------------------------------------------------------------------------------------------------------------------
					# Handles the output of exceptions on behalf of plugins integrated w/ the WebSharks™ Core.
					# --------------------------------------------------------------------------------------------------------------------------

					/**
					 * Handles a plugin exception.
					 *
					 * @see handle()
					 * @inheritdoc handle()
					 */
					protected static function handle_plugin_exception()
						{
							static::$plugin->©env->ob_end_clean();

							// Construct template data.
							$exception = static::$exception; // For template data.

							if(static::$plugin->©env->is_browser()) // Exception message (for on-site display in browsers).
								if(($template = static::$plugin->©template('exception.php', get_defined_vars())) && $template->content)
									{
										if(!headers_sent()) // Can exception stand-alone?
											{
												static::$plugin->©headers->clean_status_type(500, 'text/html', TRUE);
												exit($template->content); // Display.
											}
										else if(preg_match(static::$plugin->©template->regex_template_content_body, $template->content, $_m))
											exit(str_replace('<pre>', '<pre style="max-height:200px; overflow:auto;">', $_m['template_content_body']));
									}
							// Output exception message (command-line; and other non-browser devices).
							// This MAY also get fired if headers have already been sent, and for some reason we were unable
							// to parse a content body section from the template file; see the routine above to understand this.

							echo sprintf(static::$plugin->translate('Exception Code: %1$s'), static::$exception->getCode());

							if(static::$plugin->©env->is_in_wp_debug_display_mode() || is_super_admin())
								echo "\n".sprintf(static::$plugin->translate('Exception Message: %1$s'), static::$exception->getMessage());

							if(static::$plugin->©env->is_in_wp_debug_display_mode() || is_super_admin())
								echo "\n".static::$exception->getTraceAsString();

							exit(); // Clean exit now.
						}
				}

				# -----------------------------------------------------------------------------------------------------------------------------
				# Initialize the WebSharks™ Core exception handler.
				# -----------------------------------------------------------------------------------------------------------------------------

				exception_handler::initialize(); // Also registers the exception handler.
			}
	}