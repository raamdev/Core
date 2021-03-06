<?php
/**
 * Plugin Utilities.
 *
 * Copyright: © 2012 (coded in the USA)
 * {@link http://www.websharks-inc.com WebSharks™}
 *
 * @author JasWSInc
 * @package WebSharks\Core
 * @since 120318
 */
namespace websharks_core_v000000_dev
	{
		if(!defined('WPINC'))
			exit('Do NOT access this file directly: '.basename(__FILE__));

		/**
		 * Plugin Utilities.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @assert ($GLOBALS[__NAMESPACE__])
		 */
		class plugins extends framework
		{
			/**
			 * Performs loading sequence.
			 */
			public function load()
				{
					if(isset($this->cache[__FUNCTION__]))
						return; // Already loaded.

					$this->cache[__FUNCTION__] = TRUE;

					// Don't load the core.
					if($this->is_core()) return;

					// Fires hook before loading.
					$this->do_action('before_loaded');

					// Loads plugin.
					$this->load_classes_dir();
					$this->load_api_classes();
					$this->load_api_funcs();
					$this->load_pro_class();
					$this->check_force_activation();
					$this->©initializer->prepare_hooks();

					// Completes loading sequence.
					$this->do_action('loaded'); // We're loaded now.
					$this->do_action('after_loaded'); // Fully loaded now.
				}

			/**
			 * Loads plugin classes directory.
			 */
			public function load_classes_dir()
				{
					if(isset($this->cache[__FUNCTION__]))
						return; // Already attempted this once.

					$this->cache[__FUNCTION__] = TRUE;

					autoloader::add_classes_dir($this->___instance_config->plugin_classes_dir);
				}

			/**
			 * Loads plugin API classes.
			 */
			public function load_api_classes()
				{
					if(isset($this->cache[__FUNCTION__]))
						return; // Already attempted this once.

					$this->cache[__FUNCTION__] = TRUE;

					if(is_file($this->___instance_config->plugin_api_class_file))
						require_once $this->___instance_config->plugin_api_class_file;

					// Define `plugin_root_ns{}` in an API class file if you wish to override these defaults.

					if(!class_exists('\\'.$this->___instance_config->plugin_root_ns))
						$this->©php->¤eval('final class '.$this->___instance_config->plugin_root_ns.' extends \\'.$this->___instance_config->core_ns.'\\api{}');

					if(!class_exists('\\'.$this->___instance_config->plugin_var_ns))
						$this->©php->¤eval('class_alias(\'\\'.$this->___instance_config->plugin_root_ns.'\', \''.$this->___instance_config->plugin_var_ns.'\');');
				}

			/**
			 * Loads plugin API functions.
			 */
			public function load_api_funcs()
				{
					if(isset($this->cache[__FUNCTION__]))
						return; // Already attempted this once.

					$this->cache[__FUNCTION__] = TRUE;

					// Define these in an API class file if you wish to override these defaults.

					if(!$this->©function->is_possible('\\'.$this->___instance_config->plugin_root_ns))
						$this->©php->¤eval('function '.$this->___instance_config->plugin_root_ns.'(){ return $GLOBALS[\''.$this->___instance_config->plugin_root_ns.'\']; }');

					if(!$this->©function->is_possible('\\'.$this->___instance_config->plugin_var_ns))
						$this->©php->¤eval('function '.$this->___instance_config->plugin_var_ns.'(){ return $GLOBALS[\''.$this->___instance_config->plugin_root_ns.'\']; }');
				}

			/**
			 * Loads pro add-on class(es).
			 */
			public function load_pro_class()
				{
					if(isset($this->cache[__FUNCTION__]))
						return; // Already attempted this once.

					$this->cache[__FUNCTION__] = TRUE;

					if(is_file($this->___instance_config->plugin_pro_class_file)
					   && in_array($this->___instance_config->plugin_pro_dir_file_basename, $this->active(), TRUE)
					) // If pro add-on exists, it MUST be an active WordPress® plugin, like any other.
						{
							require_once $this->___instance_config->plugin_pro_class_file;
							$pro_class = $this->___instance_config->plugin_root_ns_prefix.'\\pro';

							if(!empty($pro_class::${'for_plugin_version'}) && $this->___instance_config->plugin_version === $pro_class::${'for_plugin_version'})
								{
									$GLOBALS[$this->___instance_config->plugin_pro_var] = $GLOBALS[$this->___instance_config->plugin_root_ns];
									autoloader::add_classes_dir($this->___instance_config->plugin_pro_classes_dir);
								}
							else $this->enqueue_pro_update_sync_notice(); // Pro add-on needs to be synchronized with current version.
						}
				}

			/**
			 * Enqueues pro update/sync notice.
			 *
			 * @note This does NOT perform any tests against the current framework and/or pro add-on.
			 *    Tests should be performed BEFORE calling upon this method. See ``load_pro_include_file_if_active()``.
			 */
			public function enqueue_pro_update_sync_notice()
				{
					if(class_exists($this->___instance_config->plugin_root_ns_prefix.'\\menu_pages\\update_sync'))
						{
							$this->©notice->enqueue( // Pro add-on needs to be synchronized with current version.
								'<p>'.$this->i18n('Your pro add-on MUST be updated now.').
								sprintf($this->i18n(' Please <a href="%1$s">click here</a> to update automatically.'), $this->©menu_page->url('update_sync', 'update_sync_pro')).
								'</p>'
							);
						}
				}

			/**
			 * Checks/forces activation of the current plugin.
			 *
			 * @note If the current plugin is NOT active at it's currently installed version;
			 *    we force activation and/or reactivation to occur on the WordPress® `setup_theme` hook, at priority `1`.
			 *    We attach to `setup_theme`, so that activation occurs before `init`, and before theme functions are loaded up.
			 *    Theme functions may include code which has plugin dependencies, so this is always a good idea.
			 */
			public function check_force_activation()
				{
					if(!$this->is_active_at_current_version())
						add_action('setup_theme', array($this, '©installer.activation'), 1);
				}

			/**
			 * Checks if the current plugin is active, at the currently installed version.
			 *
			 * @param string $reconsider Optional. Empty string default (e.g. do NOT reconsider).
			 *    You MUST use class constant {@link fw_constants::reconsider} for this argument value.
			 *    If this is {@link fw_constants::reconsider}, we force a reconsideration.
			 *
			 * @return boolean TRUE if the current plugin is active, at the currently installed version, else FALSE.
			 *
			 * @assert () === FALSE
			 * @assert (\websharks_core_v000000_dev\fw_constants::reconsider) === FALSE
			 * @assert () === FALSE
			 */
			public function is_active_at_current_version($reconsider = '')
				{
					$this->check_arg_types('string', func_get_args());

					if(!isset($this->cache[__FUNCTION__]) || $reconsider === $this::reconsider)
						{
							$this->cache[__FUNCTION__] = FALSE;

							if(($last_active_version = $this->last_active_version())
							   && version_compare($last_active_version, $this->___instance_config->plugin_version, '>=')
							) $this->cache[__FUNCTION__] = TRUE;
						}
					return $this->cache[__FUNCTION__];
				}

			/**
			 * Gets the last active version of the current plugin.
			 *
			 * @note This is set by install/activation routines for the current plugin.
			 *    This method returns the last version that was successfully activated (i.e. fully active).
			 *
			 * @return string Last active version string, else an empty string.
			 *
			 * @assert () === ''
			 */
			public function last_active_version()
				{
					return (string)get_option($this->___instance_config->plugin_root_ns_stub.'__version');
				}

			/**
			 * Checks to see if the current plugin has it's pro add-on loaded up.
			 *
			 * @return boolean TRUE if the current plugin has it's pro addon loaded up, else FALSE.
			 *
			 * @assert () === FALSE
			 */
			public function has_pro()
				{
					if(isset($GLOBALS[$this->___instance_config->plugin_pro_var])
					   && $GLOBALS[$this->___instance_config->plugin_pro_var] instanceof framework
					) return TRUE; // Yes.

					return FALSE; // Default return value.
				}

			/**
			 * Filters site transients, to allow for custom ZIP files during plugin updates.
			 *
			 * @attaches-to WordPress® filter `pre_site_transient_update_plugins`.
			 * @filter-priority `PHP_INT_MAX` After most everything else.
			 *
			 * @param boolean|mixed $transient This is passed by WordPress® as a FALSE value (initially).
			 *    However, it could be filtered by other plugins, so we need to check for an array.
			 *
			 * @return array|boolean|mixed A modified array, else the original value.
			 */
			public function pre_site_transient_update_plugins($transient)
				{
					if(is_admin() && $this->©env->is_admin_page('update.php'))
						{
							$plugin_update_zip     = $this->©vars->_REQUEST($this->___instance_config->plugin_var_ns.'_update_zip');
							$plugin_pro_update_zip = $this->©vars->_REQUEST($this->___instance_config->plugin_var_ns.'_pro_update_zip');

							if($this->©string->is_not_empty($plugin_update_zip))
								{
									if(!is_array($transient)) $transient = new \stdClass();

									$transient->last_checked                                                  = time();
									$transient->checked[$this->___instance_config->plugin_dir_file_basename]  = '000000-dev';
									$transient->response[$this->___instance_config->plugin_dir_file_basename] = (object)array(
										'id'          => 0, # N/A
										'slug'        => $this->___instance_config->plugin_dir_basename,
										'new_version' => $this->___instance_config->plugin_version,
										'url'         => $this->©menu_page->url('update_sync'),
										'package'     => $plugin_update_zip
									);
								}
							else if($this->©string->is_not_empty($plugin_pro_update_zip))
								{
									if(!is_array($transient)) $transient = new \stdClass();

									$transient->last_checked                                                      = time();
									$transient->checked[$this->___instance_config->plugin_pro_dir_file_basename]  = '000000-dev';
									$transient->response[$this->___instance_config->plugin_pro_dir_file_basename] = (object)array(
										'id'          => 0, # N/A
										'slug'        => $this->___instance_config->plugin_pro_dir_basename,
										'new_version' => $this->___instance_config->plugin_version,
										'url'         => $this->©menu_page->url('update_sync'),
										'package'     => $plugin_pro_update_zip
									);
								}
						}
					return $transient; // Possibly modified now.
				}

			/**
			 * Updates plugin site credentials (i.e. username/password for the plugin site).
			 *
			 * @param string $username Username for the plugin site.
			 *
			 * @param string $password Password for the plugin site (plain text).
			 *    This is encrypted before we store it in the database.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 */
			public function set_site_credentials($username, $password)
				{
					$this->check_arg_types('string', 'string', func_get_args());

					$credentials = array(
						'plugin_site.username' => $username,
						'plugin_site.password' => $this->©encryption->encrypt($password)
					);
					$this->©options->update($credentials);
				}

			/**
			 * Gets plugin site credentials (i.e. username/password for the plugin site).
			 *
			 * @param string  $username Optional. A new (i.e. recently submitted) username for the plugin site.
			 *
			 * @param string  $password Optional. A new (i.e. recently submitted) password for the plugin site (plain text).
			 *
			 * @param boolean $update Optional. This defaults to a FALSE value.
			 *    If this is TRUE, and ``$username`` or ``$password`` are passed in, we'll update the database with the new values.
			 *
			 * @return array Array containing two elements: `username`, `password` (plain text).
			 */
			public function get_site_credentials($username = '', $password = '', $update = FALSE)
				{
					$this->check_arg_types('string', 'string', 'boolean', func_get_args());

					$credentials = array(
						'username' => $this->©options->get('plugin_site.username'),
						'password' => $this->©encryption->decrypt($this->©options->get('plugin_site.password'))
					);
					if($username || $password) // Have new (i.e. recently submitted) values?
						{
							if($username) $credentials['username'] = $username;
							if($password) $credentials['password'] = $password;

							if($update) // Now, are we updating to these new values?
								$this->set_site_credentials($credentials['username'], $credentials['password']);
						}
					return $credentials; // Two elements: `username`, `password`.
				}

			/**
			 * The WebSharks™ Core itself?
			 *
			 * @return boolean TRUE if the current plugin is actually the WebSharks™ Core.
			 */
			public function is_core() // The WebSharks™ Core itself?
				{
					return ($this->___instance_config->plugin_root_ns === $this->___instance_config->core_ns);
				}

			/**
			 * Sets selective loading status for front-side styles.
			 *
			 * @param boolean $needs TRUE if the plugin needs front-side styles (or FALSE if it does NOT need them).
			 *    The internal default is FALSE. This MUST be set to TRUE, to enable front-side styles.
			 *
			 * @param string  $theme Optional. Defaults to an empty string.
			 *    If passed in, a specific UI theme will be enqueued or dequeued (depending on ``$needs``).
			 */
			public function needs_front_side_styles($needs, $theme = '')
				{
					$this->check_arg_types('boolean', 'string', func_get_args());

					if(!$this->©options->get('styles.front_side.load'))
						return; // Nothing to do here.

					$filter = ($needs) ? '__return_true' : '__return_false';
					remove_all_filters($this->___instance_config->plugin_root_ns_stub.'__styles__front_side');
					add_filter($this->___instance_config->plugin_root_ns_stub.'__styles__front_side', $filter);

					$components = $this->©styles->front_side_components;
					if($theme) // A specific theme will be enqueued or dequeued (depending on ``$needs``).
						$components[] = $theme; // Add to components.

					if($needs) // Enqueue or dequeue.
						$this->©styles->enqueue($components);
					else $this->©styles->dequeue($components);
				}

			/**
			 * Sets selective loading status for stand-alone styles.
			 *
			 * @param boolean $needs TRUE if the plugin needs stand-alone styles (or FALSE if it does NOT need them).
			 *    The internal default is FALSE. This MUST be set to TRUE, to enable stand-alone styles.
			 *
			 * @param string  $theme Optional. Defaults to an empty string.
			 *    If passed in, a specific UI theme will be enqueued or dequeued (depending on ``$needs``).
			 */
			public function needs_stand_alone_styles($needs, $theme = '')
				{
					$this->check_arg_types('boolean', 'string', func_get_args());

					if(!$this->©options->get('styles.front_side.load'))
						return; // Nothing to do here.

					$filter = ($needs) ? '__return_true' : '__return_false';
					remove_all_filters($this->___instance_config->plugin_root_ns_stub.'__styles__stand_alone');
					add_filter($this->___instance_config->plugin_root_ns_stub.'__styles__stand_alone', $filter);

					$components = $this->©styles->stand_alone_components;
					if($theme) // A specific theme will be enqueued or dequeued (depending on ``$needs``).
						$components[] = $theme; // Add to components.

					if($needs) // Enqueue or dequeue.
						$this->©styles->enqueue($components);
					else $this->©styles->dequeue($components);
				}

			/**
			 * Sets selective loading status for front-side scripts.
			 *
			 * @param boolean $needs TRUE if the plugin needs front-side scripts (or FALSE if it does NOT need them).
			 *    The internal default is FALSE. This MUST be set to TRUE, to enable front-side scripts.
			 */
			public function needs_front_side_scripts($needs)
				{
					$this->check_arg_types('boolean', func_get_args());

					if(!$this->©options->get('scripts.front_side.load'))
						return; // Nothing to do here.

					$filter = ($needs) ? '__return_true' : '__return_false';
					remove_all_filters($this->___instance_config->plugin_root_ns_stub.'__scripts__front_side');
					add_filter($this->___instance_config->plugin_root_ns_stub.'__scripts__front_side', $filter);

					if($needs) // Enqueue or dequeue (based on ``$needs``).
						$this->©scripts->enqueue($this->©scripts->front_side_components);
					else $this->©scripts->dequeue($this->©scripts->front_side_components);
				}

			/**
			 * Sets selective loading status for stand-alone scripts.
			 *
			 * @param boolean $needs TRUE if the plugin needs stand-alone scripts (or FALSE if it does NOT need them).
			 *    The internal default is FALSE. This MUST be set to TRUE, to enable stand-alone scripts.
			 */
			public function needs_stand_alone_scripts($needs)
				{
					$this->check_arg_types('boolean', func_get_args());

					if(!$this->©options->get('scripts.front_side.load'))
						return; // Nothing to do here.

					$filter = ($needs) ? '__return_true' : '__return_false';
					remove_all_filters($this->___instance_config->plugin_root_ns_stub.'__scripts__stand_alone');
					add_filter($this->___instance_config->plugin_root_ns_stub.'__scripts__stand_alone', $filter);

					if($needs) // Enqueue or dequeue (based on ``$needs``).
						$this->©scripts->enqueue($this->©scripts->stand_alone_components);
					else $this->©scripts->dequeue($this->©scripts->stand_alone_components);
				}

			/**
			 * Sets selective loading status for front-side scripts/styles (both at the same time).
			 *
			 * @param boolean $needs TRUE if the plugin needs front-side scripts/styles (or FALSE if it does NOT need them).
			 *    The internal default is FALSE. This MUST be set to TRUE, to enable front-side scripts/styles.
			 *
			 * @param string  $theme Optional. Defaults to an empty string.
			 *    If passed in, a specific UI theme will be enqueued or dequeued (depending on ``$needs``).
			 */
			public function needs_front_side_styles_scripts($needs, $theme = '')
				{
					$this->check_arg_types('boolean', 'string', func_get_args());

					$this->needs_front_side_styles($needs, $theme);
					$this->needs_front_side_scripts($needs);
				}

			/**
			 * Sets selective loading status for stand-alone scripts/styles (both at the same time).
			 *
			 * @param boolean $needs TRUE if the plugin needs stand-alone scripts/styles (or FALSE if it does NOT need them).
			 *    The internal default is FALSE. This MUST be set to TRUE, to enable stand-alone scripts/styles.
			 *
			 * @param string  $theme Optional. Defaults to an empty string.
			 *    If passed in, a specific UI theme will be enqueued or dequeued (depending on ``$needs``).
			 */
			public function needs_stand_alone_styles_scripts($needs, $theme = '')
				{
					$this->check_arg_types('boolean', 'string', func_get_args());

					$this->needs_stand_alone_styles($needs, $theme);
					$this->needs_stand_alone_scripts($needs);
				}

			/**
			 * Collects an array of all currently active plugins.
			 *
			 * @note This also includes active sitewide plugins in a multisite installation.
			 *
			 * @return array All currently active plugins.
			 *
			 * @assert () !empty TRUE
			 */
			public function active()
				{
					if(!isset($this->static[__FUNCTION__]))
						{
							$active = (is_array($active = get_option('active_plugins'))) ? $active : array();

							if(is_multisite() && is_array($active_sitewide_plugins = get_site_option('active_sitewide_plugins')))
								$active = array_unique(array_merge($active, $active_sitewide_plugins));

							$this->static[__FUNCTION__] = $active;
						}
					return $this->static[__FUNCTION__];
				}
		}
	}