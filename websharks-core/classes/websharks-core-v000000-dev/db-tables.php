<?php
/**
 * Database Tables.
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
		 * Database Tables.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @assert ($GLOBALS[__NAMESPACE__])
		 */
		class db_tables extends framework
		{
			/**
			 * @var string Plugin DB table prefix.
			 * @by-constructor Set dynamically by the class constructor.
			 */
			public $prefix = '';

			/**
			 * Array of plugin DB tables.
			 *
			 * @by-constructor Set dynamically by the class constructor.
			 * @extenders Extenders should use a constructor that sets this value (if applicable).
			 *
			 * @var array Defaults to an array with one generic `___unit_test_table`.
			 */
			public $tables = array('___unit_test_table');

			/**
			 * Plugin DB tables install file.
			 *
			 * @by-constructor Set dynamically by the class constructor.
			 * @extenders Extenders should implement a constructor that sets this value (if applicable).
			 *
			 * @var array Defaults to an empty string.
			 */
			public $install_file = '';

			/**
			 * Plugin DB tables upgrade file.
			 *
			 * @by-constructor Set dynamically by the class constructor.
			 * @extenders Extenders should implement a constructor that sets this value (if applicable).
			 *
			 * @var array Defaults to an empty string.
			 */
			public $upgrade_file = '';

			/**
			 * Plugin DB tables uninstall file.
			 *
			 * @by-constructor Set dynamically by the class constructor.
			 * @extenders Extenders should implement a constructor that sets this value (if applicable).
			 *
			 * @var array Defaults to an empty string.
			 */
			public $uninstall_file = '';

			/**
			 * Array of regex patterns matching DB integer columns.
			 *
			 * @extenders Extenders can override this value (when/if necessary).
			 * @var array Array of regex patterns matching DB integer columns.
			 */
			public $regex_integer_columns = array(
				'/^ID$|_id$/',
				'/(?:^|_)time(?:_|$)/',
				'/(?:^|_)(?:order|limit|count|quantity|postal_code_range)(?:_|$)/',
				'/^(?:require|unique|default|counts|recurs|taxable|listable|overrides|read_access|write_access|in_array)$/'
			);

			/**
			 * Array of regex patterns matching DB float/decimal columns.
			 *
			 * @extenders Extenders can override this value (when/if necessary).
			 * @var array Array of regex patterns matching DB float/decimal columns.
			 */
			public $regex_float_columns = array(
				'/(?:^|_)(?:amount|percentage|minimum|maximum)(?:_|$)/'
			);

			/**
			 * Array of regex patterns matching DB string columns.
			 *
			 * @extenders Extenders can override this value (when/if necessary).
			 * @var array Array of regex patterns matching DB string columns.
			 *
			 * @note All columns are strings by default. This array serves to negate
			 *    some columns which might otherwise be included in ``$regex_(integer|float)_columns``.
			 *    In other words, this array contains only a few special circumstances.
			 */
			public $regex_string_columns = array(
				'/(?:^|_)(?:subscr|txn)_id(?:_|$)/'
			);

			/**
			 * Constructor.
			 *
			 * @param object|array $___instance_config Required at all times.
			 *    A parent object instance, which contains the parent's ``$___instance_config``,
			 *    or a new ``$___instance_config`` array.
			 *
			 * @extenders Other properties should be set by class extenders.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 */
			public function __construct($___instance_config)
				{
					parent::__construct($___instance_config);

					// Other properties should be set by class extenders.

					$this->prefix = $this->prefix();
				}

			/**
			 * Database table prefix (based on current blog ID).
			 *
			 * @param string $table Optional table name. Defaults to an empty string.
			 *
			 * @return string Database table prefix.
			 */
			public function prefix($table = '')
				{
					$this->check_arg_types('string', func_get_args());

					return $this->©db->prefix.$this->___instance_config->plugin_prefix.$table;
				}

			/**
			 * Runs the plugin's SQL install/upgrade files.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully installed/upgraded, else FALSE.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert () === FALSE
			 * @assert (TRUE) === TRUE
			 */
			public function install_upgrade($confirmation = FALSE)
				{
					$this->check_arg_types('boolean', func_get_args());

					if($confirmation) // Do we have confirmation?
						{
							$install = $upgrade = NULL;

							$this->©db->is_modifying_plugin_tables(TRUE /* Flag DB class. */);

							if($this->install_file)
								foreach($this->©db_utils->prep_sql_file_queries($this->install_file) as $_query)
									if(($install = $this->©db->query($_query)) === FALSE)
										break;
							unset($_query); // Housekeeping.

							if($this->upgrade_file)
								foreach($this->©db_utils->prep_sql_file_queries($this->upgrade_file) as $_query)
									if(($upgrade = $this->©db->query($_query)) === FALSE)
										break;
							unset($_query); // Housekeeping.

							$this->©db->is_modifying_plugin_tables(FALSE /* Unflag. */);

							if($install === FALSE)
								throw $this->©exception(
									__METHOD__.'#install_failure', compact('install'),
									$this->i18n('Unable to install DB tables (install failure).')
								);
							if($upgrade === FALSE)
								throw $this->©exception(
									__METHOD__.'#upgrade_failure', compact('upgrade'),
									$this->i18n('Unable to upgrade DB tables (upgrade failure).')
								);
							return TRUE;
						}
					return FALSE;
				}

			/**
			 * Runs the plugin's SQL uninstall file.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully uninstalled, else FALSE.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert () === FALSE
			 * @assert (TRUE) === TRUE
			 */
			public function uninstall($confirmation = FALSE)
				{
					$this->check_arg_types('boolean', func_get_args());

					if($confirmation) // Do we have confirmation?
						{
							$uninstall = NULL;

							$this->©db->is_modifying_plugin_tables(TRUE /* Flag DB class. */);

							if($this->uninstall_file)
								foreach($this->©db_utils->prep_sql_file_queries($this->uninstall_file) as $_query)
									if(($uninstall = $this->©db->query($_query)) === FALSE)
										break;
							unset($_query); // Housekeeping.

							$this->©db->is_modifying_plugin_tables(FALSE /* Unflag. */);

							if($uninstall === FALSE)
								throw $this->©exception(
									__METHOD__.'#uninstall_failure', compact('uninstall'),
									$this->i18n('Unable to uninstall DB tables (uninstall failure).')
								);
							return TRUE;
						}
					return FALSE;
				}

			/**
			 * Gets the name of a plugin database table.
			 *
			 * @param string $table Unprefixed plugin table name.
			 *
			 * @return string Fully qualified (i.e. prefixed) plugin table name.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$table`` is empty, or does NOT exist.
			 *
			 * @assert ('foo') throws exception
			 * @assert ('___unit_test_table') === 'wp_ws____unit_test_table'
			 */
			public function get($table)
				{
					$this->check_arg_types('string:!empty', func_get_args());

					if(in_array($table, $this->tables, TRUE))
						return $this->prefix($table);

					throw $this->©exception(
						__METHOD__.'#unknown_db_table', compact('table'),
						sprintf($this->i18n('Unknown plugin DB table: `%1$s`.'), $table).
						sprintf($this->i18n(' Current plugin tables include: `%1$s`.'), $this->©var->dump($this->tables))
					);
				}

			/**
			 * Gets the name of a WordPress® database table.
			 *
			 * @param string $table Unprefixed WordPress® table name.
			 *
			 * @return string Fully qualified (i.e. prefixed) WordPress® table name.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$table`` is empty, or does NOT exist.
			 *
			 * @assert ('options') === 'wp_options'
			 */
			public function get_wp($table)
				{
					$this->check_arg_types('string:!empty', func_get_args());

					if(!empty($this->©db->$table) && is_string($this->©db->$table))
						return $this->©db->$table; // WordPress® table.

					throw $this->©exception(
						__METHOD__.'#unknown_wp_db_table', compact('table'),
						sprintf($this->i18n('Unknown WordPress® DB table: `%1$s`.'), $table)
					);
				}

			/**
			 * Plugin DB tables (imploded for a regex pattern).
			 */
			public function imploded_for_regex()
				{
					if(!isset($this->cache['imploded_for_regex']))
						{
							$tables                            = $this->©strings->wrap_deep($this->tables, $this->prefix, '', FALSE);
							$tables                            = $this->©strings->preg_quote_deep($tables, '/');
							$this->cache['imploded_for_regex'] = implode('|', $tables);
						}
					return $this->cache['imploded_for_regex'];
				}

			/**
			 * Adds data/procedures associated with this class.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully installed, else FALSE.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert () === FALSE
			 * @assert (TRUE) === TRUE
			 */
			public function activation_install($confirmation = FALSE)
				{
					$this->check_arg_types('boolean', func_get_args());

					if($confirmation) // Do we have confirmation?
						{
							$this->install_upgrade(TRUE);

							return TRUE;
						}
					return FALSE; // Default return value.
				}

			/**
			 * Removes data/procedures associated with this class.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully uninstalled, else FALSE.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert () === FALSE
			 * @assert (TRUE) === TRUE
			 */
			public function deactivation_uninstall($confirmation = FALSE)
				{
					$this->check_arg_types('boolean', func_get_args());

					if($confirmation) // Do we have confirmation?
						{
							$this->uninstall(TRUE);

							return TRUE;
						}
					return FALSE; // Default return value.
				}
		}
	}