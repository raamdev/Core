<?php
/**
 * Directory Utilities.
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
		 * Directory Utilities.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @assert ($GLOBALS[__NAMESPACE__])
		 */
		class dirs extends framework
		{
			/**
			 * Normalizes directory separators in directory/file paths.
			 *
			 * @param string  $path Directory or file path.
			 *
			 * @param boolean $allow_trailing_slash Defaults to FALSE.
			 *    If TRUE; and ``$path`` contains a trailing slash; we'll leave it there.
			 *
			 * @return string Directory or file path, after having been normalized by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert ('\\path/to\\something\\') === '/path/to/something'
			 * @assert ('\\path//to\\some\\file.php') === '/path/to/some/file.php'
			 */
			public function n_seps($path, $allow_trailing_slash = FALSE)
				{
					$this->check_arg_types('string', 'boolean', func_get_args());

					preg_match('/^(?P<scheme>[a-z]+\:\/\/)/i', $path, $_path);
					$path = (!empty($_path['scheme'])) ? str_ireplace($_path['scheme'], '', $path) : $path;

					$path = preg_replace('/\/+/', '/', str_replace(array(DIRECTORY_SEPARATOR, '\\', '/'), '/', $path));
					$path = ($allow_trailing_slash) ? $path : rtrim($path, '/');

					$path = (!empty($_path['scheme'])) ? strtolower($_path['scheme']).$path : $path; // Lowercase.

					return $path; // Normalized now.
				}

			/**
			 * Normalizes directory separators in directory/file paths.
			 *
			 * @static For the WebSharks™ Core framework constructor.
			 *    Must NOT have any other core dependencies.
			 *
			 * @param string  $path Directory or file path.
			 *
			 * @param boolean $allow_trailing_slash Defaults to FALSE.
			 *    If TRUE; and ``$path`` contains a trailing slash; we'll leave it there.
			 *
			 * @return string Directory or file path, after having been normalized by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 */
			public static function _4fwc_n_seps($path, $allow_trailing_slash = FALSE)
				{
					$path = (string)$path;

					preg_match('/^(?P<scheme>[a-z]+\:\/\/)/i', $path, $_path);
					$path = (!empty($_path['scheme'])) ? str_ireplace($_path['scheme'], '', $path) : $path;

					$path = preg_replace('/\/+/', '/', str_replace(array(DIRECTORY_SEPARATOR, '\\', '/'), '/', $path));
					$path = ($allow_trailing_slash) ? $path : rtrim($path, '/');

					$path = (!empty($_path['scheme'])) ? strtolower($_path['scheme']).$path : $path; // Lowercase.

					return $path; // Normalized now.
				}

			/**
			 * Strips a trailing `/app_data/` sub-directory.
			 *
			 * @param string $path Directory or file path.
			 *
			 * @return string Directory or file path without `/app_data/`.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert ('/path/to/dir\\app_data/') === '/path/to/dir'
			 */
			public function rtrim_app_data($path)
				{
					$this->check_arg_types('string', func_get_args());

					return preg_replace('/\/app_data$/', '', $this->n_seps($path));
				}

			/**
			 * Basename from a full directory or file path.
			 *
			 * @param string $path Directory or file path.
			 *
			 * @return string Basename, including a possible `/app_data/` directory.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @assert ('/path/to/dir/app_data\\') === 'dir/app_data'
			 * @assert ('/path/to/dir/app_data/') === 'dir/app_data'
			 * @assert ('/path/to/dir/app_data/dir') === 'dir'
			 */
			public function get_base_app_data($path)
				{
					$this->check_arg_types('string', func_get_args());

					$path = preg_replace('/\/app_data$/', '', $this->n_seps($path), 1, $app_data);

					return basename($path).(($app_data) ? '/app_data' : '');
				}

			/**
			 * Shortens a directory or file path to its relative location from `DOCUMENT_ROOT`.
			 *
			 * @param string  $to The full directory or file path, which this routine will build a relative path ``$to``.
			 *
			 * @param boolean $try_realpaths Defaults to TRUE. When TRUE, try to acquire ``realpath()``,
			 *    thereby resolving all relative paths and/or symlinks in `DOCUMENT_ROOT` and ``$to``.
			 *
			 * @param boolean $use_win_diff_drive_jctn Defaults to TRUE. When TRUE, we'll try to work around issues with different drives on Windows®,
			 *    by attempting to create a directory junction between the two different drives; so a relative path can be formulated properly.
			 *
			 * @return string String with relative path to: ``$to`` (from: `DOCUMENT_ROOT`), else an empty string on failure.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If `DOCUMENT_ROOT` is empty, or is NOT a string.
			 *
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/path/to/a/dir/') === 'path/to/a/dir'
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/path/to/a/file.php') === 'path/to/a/file.php'
			 */
			public function get_doc_root_path($to, $try_realpaths = TRUE, $use_win_diff_drive_jctn = TRUE)
				{
					$this->check_arg_types('string', 'boolean', 'boolean', func_get_args());

					if(is_string($doc_root = $this->©vars->_SERVER('DOCUMENT_ROOT')) && $doc_root)
						return $this->get_rel_path($doc_root, $to, $try_realpaths, $use_win_diff_drive_jctn);

					else throw $this->©exception(
						__METHOD__.'#doc_root_missing', compact('doc_root'),
						$this->i18n('Invalid and/or empty `DOCUMENT_ROOT` (expecting string NOT empty).')
					);
				}

			/**
			 * Shortens a directory or file path to its relative location.
			 *
			 * @param string  $from The full directory or file path to calculate a relative path ``$from``.
			 *
			 * @param string  $to The full directory or file path, which this routine will build a relative path ``$to``.
			 *
			 * @param boolean $try_realpaths Defaults to TRUE. When TRUE, try to acquire ``realpath()``,
			 *    thereby resolving all relative paths and/or symlinks in ``$from`` and ``$to`` args.
			 *
			 * @param boolean $use_win_diff_drive_jctn Defaults to TRUE. When TRUE, we'll try to work around issues with different drives on Windows®,
			 *    by attempting to create a directory junction between the two different drives; so a relative path can be formulated properly.
			 *
			 * @return string String with relative path to: ``$to`` (from: ``$from``), else an empty string on failure.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If Windows® drive issues cannot be resolved in any way.
			 *
			 * @assert ($_SERVER['DOCUMENT_ROOT'], $_SERVER['DOCUMENT_ROOT'].'/wp-content/') === 'wp-content'
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/', $_SERVER['DOCUMENT_ROOT'].'/wp-content/') === '../'
			 * @assert ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/index.php', $_SERVER['DOCUMENT_ROOT'].'/wp-content/index.php') === '../index.php'
			 * @assert ('/path/to/a/b/c/', '/path/to/a/b/c/d/e/file.php') === 'd/e/file.php'
			 * @assert ('/path/to/a/b/c/', '/path/to/a/file.php') === '../../file.php'
			 */
			public function get_rel_path($from, $to, $try_realpaths = TRUE, $use_win_diff_drive_jctn = TRUE)
				{
					$this->check_arg_types('string', 'string', 'boolean', 'boolean', func_get_args());

					if(!$from || !$to) // Nothing to do.
						return ''; // Default return value.

					if($try_realpaths && ($_real_from = realpath($from)) && ($_real_to = realpath($to)))
						{
							$from = $_real_from; // Real path on server.
							$to   = $_real_to; // Use real path on server.
						}
					unset($_real_from, $_real_to); // Housekeeping.

					$from = (is_file($from) || // Or if we can match one of these common extensions.
					         preg_match('/\.(:?txt|html|php|css|js|png|jpg|gif|swf|mp[34]|wav|zip|tar|gz)$/', $from))
						? dirname($from) : $from;
					$from = preg_split('/\//', $this->n_seps($from));
					$to   = preg_split('/\//', $this->n_seps($to));

					if(stripos(PHP_OS, 'win') === 0) // Handle Windows® drive issues here.
						{
							if(preg_match('/^(?P<drive_letter>[A-Z])\:$/i', $from[0], $_m))
								$_from_drive = $_m['drive_letter'];

							if(preg_match('/^(?P<drive_letter>[A-Z])\:$/i', $to[0], $_m))
								$_to_drive = $_m['drive_letter'];

							if(!empty($_from_drive) && empty($_to_drive))
								{
									$_to_drive = $_from_drive;
									if(empty($to[0]))
										$to[0] = $_to_drive.':';
									else array_unshift($to, $_to_drive.':');
								}
							else if(!empty($_to_drive) && empty($_from_drive))
								{
									$_from_drive = $_to_drive;
									if(empty($from[0]))
										$from[0] = $_from_drive.':';
									else array_unshift($from, $_from_drive.':');
								}

							if($use_win_diff_drive_jctn) // Is this feature enabled? Hopefully so.
								{
									if(isset($_from_drive, $_to_drive) && strcasecmp($_from_drive, $_to_drive) !== 0)
										{
											$_from_drive_jctn = $_from_drive.':/websharks-'.$_to_drive.'-jctn';
											if(($_sys_temp_dir = $this->get_sys_temp_dir()) && strcasecmp($_from_drive, $_sys_temp_dir[0]) === 0)
												$_sys_temp_dir_jctn = $_sys_temp_dir.'/websharks-'.$_to_drive.'-jctn';

											$_jctn = (is_dir($_from_drive_jctn)) ? $_from_drive_jctn : NULL;
											if(!$_jctn && !empty($_sys_temp_dir_jctn) && is_dir($_sys_temp_dir_jctn))
												$_jctn = $_sys_temp_dir_jctn;

											if($_jctn // We already have a junction created?
											   || ($_jctn = $this->create_win_jctn($_from_drive_jctn, $_to_drive.':/')) #
											   || (!empty($_sys_temp_dir_jctn) && ($_jctn = $this->create_win_jctn($_sys_temp_dir_jctn, $_to_drive.':/')))
											) // If any of these succeeded, we can use the Directory Junction now.
												{
													array_shift($to); // Shift drive off and use junction now.
													foreach(array_reverse(preg_split('/\//', $_jctn)) as $_jctn_dir)
														array_unshift($to, $_jctn_dir);
												}
											else throw $this->©exception(
												__METHOD__.'#windows_drive', compact('_from_drive', '_to_drive'),
												$this->i18n('Unable to generate a relative path across different Windows® drives.').
												sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $_from_drive_jctn, $_to_drive.':/')
											);
										}
								}
							else if(isset($_from_drive, $_to_drive) && strcasecmp($_from_drive, $_to_drive) !== 0)
								{
									throw $this->©exception(
										__METHOD__.'#windows_drive', compact('_from_drive', '_to_drive'),
										$this->i18n('Unable to generate a relative path across different Windows® drives.').
										sprintf($this->i18n(' Drive from: `%1$s`, drive to: `%2$s`.'), $_from_drive.':/', $_to_drive.':/')
									);
								}
							unset($_m, $_from_drive, $_to_drive, $_from_drive_jctn, $_sys_temp_dir, $_sys_temp_dir_jctn, $_jctn, $_jctn_dir);
						}

					foreach(array_keys($from) as $_depth) // Loop through each ``$from`` directory ``$_depth``.
						if(isset($from[$_depth], $to[$_depth]) && $from[$_depth] === $to[$_depth])
							unset($from[$_depth], $to[$_depth]);
						else break;

					$to = implode('/', $to);
					for($_depth = 0; $_depth < count($from); $_depth++)
						$to = '../'.$to;

					unset($_depth); // A little housekeeping.

					return $to; // Relative path.
				}

			/**
			 * Checks if a directory or file path is actually a link.
			 *
			 * @param string $dir_file Directory or file path (i.e. a possible symlink).
			 *
			 * @return boolean TRUE if ``$dir_file`` is a link; else FALSE.
			 */
			public function is_link($dir_file)
				{
					$this->check_arg_types('string', func_get_args());

					if(!$dir_file) return FALSE; // Catch empty values here.

					$dir_file = $this->n_seps($dir_file);
					$realpath = (is_dir($dir_file) || is_file($dir_file)) ? $this->n_seps(realpath($dir_file)) : '';

					return ($dir_file && $realpath && $dir_file !== $realpath);
				}

			/**
			 * Creates a directory Junction in Windows®.
			 *
			 * @param string $jctn Directory location of the Junction (i.e. the symlink).
			 *
			 * @param string $target Target directory that this Junction will connect to.
			 *
			 * @return string Location of the Junction; else an empty string on failure.
			 *    If the Junction already exists as a link (and it already points to the ``$target``);
			 *    we simply return ``$jctn`` without any change (e.g. there is nothing more to do in that scenario).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$jctn`` exists already and it's an existing directory or file (i.e. NOT a link).
			 * @throws exception If ``$jctn`` exists and it's NOT writable (or we are unable to remove it before recreating).
			 * @throws exception If ``$target`` does NOT exist; or if it does exist, but it's NOT a directory.
			 * @throws exception If the Junction needs to be created; but Windows® commands are NOT possible.
			 * @throws exception If creation of a Directory Junction fails for any reason.
			 *
			 * @windows-assert ('C:/websharks-jctn-tester', 'E:/') === 'C:/websharks-jctn-tester'
			 * @assert ('C:/websharks-jctn-tester', 'E:/') === ''
			 */
			public function create_win_jctn($jctn, $target)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', func_get_args());

					$jctn   = $this->n_seps($jctn);
					$target = $this->n_seps($target);

					$is_jctn_dir   = is_dir($jctn);
					$is_target_dir = is_dir($target);
					$is_jctn_link  = $this->is_link($jctn);

					$realpath_jctn   = ($is_jctn_dir) ? $this->n_seps(realpath($jctn)) : '';
					$realpath_target = ($is_target_dir) ? $this->n_seps(realpath($target)) : '';

					if($is_jctn_link && $is_jctn_dir && $is_target_dir && $realpath_jctn && $realpath_target && $realpath_jctn === $realpath_target)
						return $jctn; // It already exists; and it's already pointing to the proper location.

					if(!$this->delete_win_jctn($jctn))
						throw $this->©exception(
							__METHOD__.'#jctn_exists', compact('jctn', 'target'),
							$this->i18n('Unable to create a Windows® Directory Junction. Already exists.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);
					else if(!$is_target_dir)
						throw $this->©exception(
							__METHOD__.'#target_not_dir', compact('jctn', 'target'),
							$this->i18n('Unable to create a Windows® Directory Junction. Invalid target.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);

					if(!$this->©commands->windows_possible())
						throw $this->©exception(
							__METHOD__.'#not_possible', compact('jctn', 'target'),
							$this->i18n('Not possible to create a Windows® Directory Junction.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);
					$mklink_args = $this->©commands->mklink.' /J '.escapeshellarg($jctn).' '.escapeshellarg($target);
					$mklink      = $this->©commands->exec($mklink_args, '', TRUE);

					$mklink_status = $mklink['status'];
					$mklink_errors = $mklink['errors'];
					/** @var errors $mklink_errors */

					if($mklink_status !== 0 || $mklink_errors->exist())
						throw $this->©exception(
							__METHOD__.'#issue', get_defined_vars(),
							sprintf($this->i18n('The command: `%1$s`, returned a non-zero status or error. Mklink said: `%2$s`'),
							        $mklink_args, $mklink_errors->get_message())
						);

					clearstatcache(); // Clear cache.

					if(!is_dir($jctn)) // Now we test this again.
						throw $this->©exception(
							__METHOD__.'#failure', compact('jctn', 'target'),
							$this->i18n('Failed to create a Windows® Directory Junction.').
							sprintf($this->i18n(' Please create a Directory Junction here: `%1$s`, pointing to: `%2$s`.'), $jctn, $target)
						);
					return $jctn; // It's a good day in Eureka!
				}

			/**
			 * Deletes a Windows® Directory Junction.
			 *
			 * @param string $jctn Directory location of the Junction (i.e. the symlink).
			 *
			 * @return boolean TRUE if Directory Junction is NOT a directory or file; or if it was deleted successfully.
			 *    Else this will throw an exception. We either return TRUE; or we throw an exception.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$jctn`` exists already and it's an actual directory/file (i.e. NOT a link).
			 * @throws exception If ``$jctn`` exists and it's NOT writable (or we are unable to remove it).
			 * @throws exception If deletion of a Directory Junction fails for any reason.
			 */
			public function delete_win_jctn($jctn)
				{
					$this->check_arg_types('string:!empty', func_get_args());

					$jctn         = $this->n_seps($jctn);
					$is_jctn_link = $this->is_link($jctn);
					$is_jctn_file = is_file($jctn);
					$is_jctn_dir  = is_dir($jctn);

					if(!$is_jctn_link && !$is_jctn_file && !$is_jctn_dir)
						return TRUE; // It's already gone :-)

					if($is_jctn_file)
						throw $this->©exception(
							__METHOD__.'#file', compact('jctn'),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This is NOT a Directory Junction (it\'s a file): `%1$s`.'), $jctn)
						);
					else if(!$is_jctn_dir)
						throw $this->©exception(
							__METHOD__.'#not_dir', compact('jctn'),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This is NOT a Directory Junction: `%1$s`.'), $jctn)
						);
					else if(!$is_jctn_link)
						throw $this->©exception(
							__METHOD__.'#not_link', compact('jctn'),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This is NOT a Directory Junction: `%1$s`.'), $jctn)
						);

					if(!is_writable($jctn))
						throw $this->©exception(
							__METHOD__.'#permissions', compact('jctn'),
							$this->i18n('Unable to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' This Directory Junction is NOT writable: `%1$s`.'), $jctn)
						);

					if(!$this->©commands->windows_possible())
						throw $this->©exception(
							__METHOD__.'#not_possible', compact('jctn'),
							$this->i18n('Not possible to delete a Windows® Directory Junction.').
							sprintf($this->i18n(' Please delete this Directory Junction: `%1$s`.'), $jctn)
						);
					$rmdir_args = $this->©commands->rmdir.' '.escapeshellarg(str_replace('/', '\\', $jctn));
					$rmdir      = $this->©commands->exec($rmdir_args, '', TRUE);

					$rmdir_status = $rmdir['status'];
					$rmdir_errors = $rmdir['errors'];
					/** @var errors $rmdir_errors */

					if($rmdir_status !== 0 || $rmdir_errors->exist())
						throw $this->©exception(
							__METHOD__.'#issue', get_defined_vars(),
							sprintf($this->i18n('The command: `%1$s`, returned a non-zero status or error. Rmdir said: `%2$s`'),
							        $rmdir_args, $rmdir_errors->get_message())
						);
					return TRUE; // Default return value.
				}

			/**
			 * Get the system's temporary directory.
			 *
			 * @param boolean $fallback_on_wp_temp_dir This defaults to FALSE (for better security).
			 *    If TRUE, we'll fall back on the WordPress® ``get_temp_dir()`` function. A fallback may occur when/if we're unable
			 *    to find the system's temporary directory. Or, if the system's temporary directory is NOT readable/writable.
			 *
			 * @return string Full path to a readable/writable temp directory, else an empty string on failure.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If unable to find a readable/writable directory, for any reason.
			 *
			 * @assert () !== ''
			 * @assert (TRUE) !== ''
			 * @assert (FALSE) !== ''
			 */
			public function get_sys_temp_dir($fallback_on_wp_temp_dir = FALSE)
				{
					$this->check_arg_types('boolean', func_get_args());

					if(($sys_temp_dir = sys_get_temp_dir())
					   && ($sys_temp_dir = realpath($sys_temp_dir))
					   && is_readable($sys_temp_dir) && is_writable($sys_temp_dir)
					) // This is the ideal location for temporary files (best security here).
						return $this->n_seps($sys_temp_dir);

					else if(($upload_temp_dir = ini_get('upload_tmp_dir'))
					        && ($upload_temp_dir = realpath($upload_temp_dir))
					        && is_readable($upload_temp_dir) && is_writable($upload_temp_dir)
					) // Secondary location for temporary files (marginal security here).
						return $this->n_seps($upload_temp_dir);

					else if($fallback_on_wp_temp_dir
					        && ($wp_temp_dir = get_temp_dir())
					        && ($wp_temp_dir = realpath($wp_temp_dir))
					        && is_readable($wp_temp_dir) && is_writable($wp_temp_dir)
					) // This may offer FAR less security.
						return $this->n_seps($wp_temp_dir);

					throw $this->©exception(
						__METHOD__.'#missing', compact('sys_temp_dir', 'upload_temp_dir', 'wp_temp_dir'),
						$this->i18n('Unable to find a readable/writable temp directory (please check server configuration).')
					);
				}

			/**
			 * Get temporary directory for WordPress®.
			 *
			 * @return string Full path to a readable/writable temp directory, else an empty string on failure.
			 *
			 * @throws exception If unable to find a readable/writable directory, for any reason.
			 *
			 * @assert () !== ''
			 */
			public function get_wp_temp_dir()
				{
					if(($wp_temp_dir = get_temp_dir())
					   && ($wp_temp_dir = realpath($wp_temp_dir))
					   && is_readable($wp_temp_dir) && is_writable($wp_temp_dir)
					) return $this->n_seps($wp_temp_dir);

					throw $this->©exception(
						__METHOD__.'#missing', compact('wp_temp_dir'),
						$this->i18n('Unable to find a readable/writable temp directory (please check server configuration).')
					);
				}

			/**
			 * Calculates the MD5 checksum for an entire directory recursively.
			 *
			 * @param string  $dir The directory we should begin with.
			 *
			 * @param boolean $update_checksum_file Defaults to a FALSE value.
			 *    If this is TRUE, the `checksum.txt` file in the root directory @ ``$depth`` zero, will be updated.
			 *    If the `checksum.txt` file does NOT exist yet, this routine will attempt to create it.
			 *
			 * @param string  $root_dir Internal parameter. Defaults to an empty string, indicating the current ``$dir``.
			 *    Recursive calls to this method will automatically pass this value, indicating the main root directory value.
			 *
			 * @return string An MD5 checksum established collectively, based on all directories/files.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$dir`` is NOT a directory, or is not readable.
			 * @throws exception If ``$update_checksum_file`` is TRUE, ``$depth`` is zero,
			 *    and permission issues (of any kind) prevent the update or creation of the `checksum.txt` file.
			 */
			public function checksum($dir, $update_checksum_file = FALSE, $root_dir = '')
				{
					$this->check_arg_types('string', 'boolean', 'string', func_get_args());

					$checksums = array(); // Initialize array.

					if(!is_dir($dir) || !is_readable($dir) || !($handle = opendir($dir)))
						throw $this->©exception(
							__METHOD__.'#cannot_read_dir', NULL,
							sprintf($this->i18n('Unable to read directory: `%1$s`'), $dir)
						);

					$dir          = $this->n_seps(realpath($dir));
					$root_dir     = (!$root_dir) ? $dir : $this->n_seps(realpath($root_dir));
					$relative_dir = preg_replace('/^'.preg_quote($root_dir, '/').'(?:\/|$)/', '', $dir);

					$checksums[$relative_dir] = md5($relative_dir); // Relative directory checksum.

					while(($entry = readdir($handle)) !== FALSE)
						if($entry !== '.' && $entry !== '..') // Ignore single/double dots.
							if($entry !== 'checksum.txt' || $dir !== $root_dir) // Skip in root directory.
								{
									if(is_dir($dir.'/'.$entry))
										$checksums[$relative_dir.'/'.$entry] = $this->checksum($dir.'/'.$entry, FALSE, $root_dir);
									else $checksums[$relative_dir.'/'.$entry] = md5($relative_dir.'/'.$entry.md5_file($dir.'/'.$entry));
								}
					closedir($handle); // Close directory handle now.

					ksort($checksums, SORT_STRING); // In case order changes from one server to another.
					$checksum = md5(implode('', $checksums)); // Collective checksum.

					if($update_checksum_file && $dir === $root_dir) // Updating?
						{
							if(!file_exists($dir.'/checksum.txt') && !is_writable($dir))
								throw $this->©exception(
									__METHOD__.'#permission_issue', compact('dir'),
									sprintf($this->i18n('Need this directory to be writable: `%1$s`'), $dir)
								);
							else if(file_exists($dir.'/checksum.txt') && !is_writable($dir.'/checksum.txt'))
								throw $this->©exception(
									__METHOD__.'#permission_issue', compact('dir'),
									sprintf($this->i18n('Need this file to be writable: `%1$s`'), $dir.'/checksum.txt')
								);
							file_put_contents($dir.'/checksum.txt', $checksum);
						}
					return $checksum; // Collective checksum.
				}

			/**
			 * Get cache directory (public or private).
			 *
			 * @param string  $type The type of cache directory (`public` or `private`).
			 *
			 * @param string  $sub_dir Optional cache sub-directory path.
			 *
			 * @return string Full path to a readable/writable cache directory, else an exception is thrown on failure.
			 *    If the directory does NOT yet exist, it's created by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use: `public` or `private`.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 * @throws exception If the requested cache directory is NOT readable/writable, or CANNOT be created for any reason.
			 *
			 * @assert ('public') === $GLOBALS[__NAMESPACE__]->___instance_config->plugin_data_dir.'/cache/public'
			 * @assert ('private') === $GLOBALS[__NAMESPACE__]->___instance_config->plugin_data_dir.'/cache/private'
			 * @assert ('foo') throws exception
			 */
			public function get_cache_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string', 'string', func_get_args());

					if(in_array($type, array('public', 'private'), TRUE))
						{
							// Creates a possible ``$sub_dir`` appendage.
							if($sub_dir && ($sub_dir = $this->©strings->trim($this->n_seps($sub_dir), '', '/')))
								{
									if(strpos($sub_dir, '..') === FALSE) // No relative paths please.
										$sub_dir = '/'.$sub_dir; // Add prefix so it can be appended easily.

									else throw $this->©exception(
										__METHOD__.'#relative_paths', compact('sub_dir'),
										$this->i18n('Expecting a sub-directory with NO relative paths.').
										sprintf($this->i18n(' Instead got: `%1$s`.'), $sub_dir)
									);
								}

							// Clean these up and piece them together.
							$cache_dir          = $this->n_seps($this->___instance_config->plugin_data_dir.'/cache');
							$app_data_sub_dir   = ($type === 'private' && $this->©env->is_windows() && !$this->©env->is_apache()) ? '/app_data' : '';
							$cache_type_sub_dir = $this->n_seps($cache_dir.'/'.$type.$app_data_sub_dir.$sub_dir);

							// Need to create the ``$cache_dir``?
							if(!is_dir($cache_dir)) // Create if missing.
								{
									mkdir($cache_dir, 0775, TRUE);
									clearstatcache(); // Clear cache (makes other routines aware).

									if(!is_dir($cache_dir) || !is_readable($cache_dir) || !is_writable($cache_dir))
										throw $this->©exception(
											__METHOD__.'#read_write_issues', compact('cache_dir'),
											$this->i18n('Unable to create a readable/writable `cache` directory.').
											sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $cache_dir)
										);
								}

							// Need to create the ``$cache_type_sub_dir``?
							if(!is_dir($cache_type_sub_dir)) // Create if missing.
								{
									mkdir($cache_type_sub_dir, 0775, TRUE);
									clearstatcache(); // Clear cache (makes other routines aware).

									if(!is_dir($cache_type_sub_dir) || !is_readable($cache_type_sub_dir) || !is_writable($cache_type_sub_dir))
										throw $this->©exception(
											__METHOD__.'#read_write_issues', compact('cache_type_sub_dir'),
											$this->i18n('Unable to create a readable/writable cache type sub-directory.').
											sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $cache_type_sub_dir)
										);

									if(is_dir($cache_dir.'/private') && !file_exists($cache_dir.'/private/.htaccess'))
										file_put_contents($cache_dir.'/private/.htaccess', 'deny from all');
								}

							// Is ``$cache_type_sub_dir`` writable?
							if(is_readable($cache_type_sub_dir) && is_writable($cache_type_sub_dir))
								return $cache_type_sub_dir; // It's a good day in Eureka!

							else throw $this->©exception(
								__METHOD__.'#read_write_issues', compact('cache_type_sub_dir'),
								$this->i18n('Unable to find a readable/writable cache type sub-directory.').
								sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $cache_type_sub_dir)
							);
						}
					else throw $this->©exception(
						__METHOD__.'#invalid_type', compact('type'),
						$this->i18n('Invalid cache type sub-directory. Expecting `public` or `private`.').
						sprintf($this->i18n(' Instead got: `%1$s`.'), $type)
					);
				}

			/**
			 * Empties and deletes a cache directory (public or private).
			 *
			 * @param string  $type The type of cache directory (`public` or `private`).
			 *
			 * @param string  $sub_dir Optional cache sub-directory path.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else FALSE.
			 *    Also returns TRUE if the directory is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use: `public` or `private`.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 *
			 * @throws exception See: ``get_cache_dir()`` for additional exceptions this may throw.
			 * @throws exception See: ``empty_and_remove()`` for additional exceptions this may throw.
			 *
			 * @assert ('public') === TRUE
			 * @assert ('private') === TRUE
			 * @assert ('foo') throws exception
			 */
			public function del_cache_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string', 'string', func_get_args());

					return $this->empty_and_remove($this->get_cache_dir($type, $sub_dir));
				}

			/**
			 * Get log directory (public or private).
			 *
			 * @param string  $type The type of log directory (`public` or `private`).
			 *
			 * @param string  $sub_dir Optional log sub-directory path.
			 *
			 * @return string Full path to a readable/writable log directory, else an exception is thrown on failure.
			 *    If the directory does NOT yet exist, it's created by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use: `public` or `private`.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 * @throws exception If the requested log directory is NOT readable/writable, or CANNOT be created for any reason.
			 *
			 * @assert ('public') === $GLOBALS[__NAMESPACE__]->___instance_config->plugin_data_dir.'/logs/public'
			 * @assert ('private') === $GLOBALS[__NAMESPACE__]->___instance_config->plugin_data_dir.'/logs/private'
			 * @assert ('foo') throws exception
			 */
			public function get_log_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string', 'string', func_get_args());

					if(in_array($type, array('public', 'private'), TRUE))
						{
							// Creates a possible ``$sub_dir`` appendage.
							if($sub_dir && ($sub_dir = $this->©strings->trim($this->n_seps($sub_dir), '', '/')))
								{
									if(strpos($sub_dir, '..') === FALSE) // No relative paths please.
										$sub_dir = '/'.$sub_dir; // Add prefix so it can be appended easily.

									else throw $this->©exception(
										__METHOD__.'#relative_paths', compact('sub_dir'),
										$this->i18n('Expecting a sub-directory with NO relative paths.').
										sprintf($this->i18n(' Instead got: `%1$s`.'), $sub_dir)
									);
								}

							// Clean these up and piece them together.
							$logs_dir         = $this->n_seps($this->___instance_config->plugin_data_dir.'/logs');
							$app_data_sub_dir = ($type === 'private' && $this->©env->is_windows() && !$this->©env->is_apache()) ? '/app_data' : '';
							$log_type_sub_dir = $this->n_seps($logs_dir.'/'.$type.$app_data_sub_dir.$sub_dir);

							// Need to create the ``$logs_dir``?
							if(!is_dir($logs_dir)) // Create if missing.
								{
									mkdir($logs_dir, 0775, TRUE);
									clearstatcache(); // Clear cache (makes other routines aware).

									if(!is_dir($logs_dir) || !is_readable($logs_dir) || !is_writable($logs_dir))
										throw $this->©exception(
											__METHOD__.'#read_write_issues', compact('logs_dir'),
											$this->i18n('Unable to create a readable/writable `logs` directory.').
											sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $logs_dir)
										);
								}

							// Need to create the ``$log_type_sub_dir``?
							if(!is_dir($log_type_sub_dir)) // Create if missing.
								{
									mkdir($log_type_sub_dir, 0775, TRUE);
									clearstatcache(); // Clear cache (makes other routines aware).

									if(!is_dir($log_type_sub_dir) || !is_readable($log_type_sub_dir) || !is_writable($log_type_sub_dir))
										throw $this->©exception(
											__METHOD__.'#read_write_issues', compact('log_type_sub_dir'),
											$this->i18n('Unable to create a readable/writable log type sub-directory.').
											sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $log_type_sub_dir)
										);

									if(is_dir($logs_dir.'/private') && !file_exists($logs_dir.'/private/.htaccess'))
										file_put_contents($logs_dir.'/private/.htaccess', 'deny from all');
								}

							// Is ``$log_type_sub_dir`` readable/writable?
							if(is_readable($log_type_sub_dir) && is_writable($log_type_sub_dir))
								return $log_type_sub_dir; // It's a good day in Eureka!

							else throw $this->©exception(
								__METHOD__.'#read_write_issues', compact('log_type_sub_dir'),
								$this->i18n('Unable to find a readable/writable log type sub-directory.').
								sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $log_type_sub_dir)
							);
						}
					else throw $this->©exception(
						__METHOD__.'#invalid_type', compact('type'),
						$this->i18n('Invalid log type sub-directory. Expecting `public` or `private`.').
						sprintf($this->i18n(' Instead got: `%1$s`.'), $type)
					);
				}

			/**
			 * Empties and deletes a log directory (public or private).
			 *
			 * @param string  $type The type of log directory (`public` or `private`).
			 *
			 * @param string  $sub_dir Optional log sub-directory path.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else FALSE.
			 *    Also returns TRUE if the directory is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If an invalid ``$type`` is passed in. Use: `public` or `private`.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 *
			 * @throws exception See: ``get_log_dir()`` for additional exceptions this may throw.
			 * @throws exception See: ``empty_and_remove()`` for additional exceptions this may throw.
			 *
			 * @assert ('public') === TRUE
			 * @assert ('private') === TRUE
			 * @assert ('foo') throws exception
			 */
			public function del_log_dir($type, $sub_dir = '')
				{
					$this->check_arg_types('string', 'string', func_get_args());

					return $this->empty_and_remove($this->get_log_dir($type, $sub_dir));
				}

			/**
			 * Gets a private media directory.
			 *
			 * @param string  $sub_dir Optional private media sub-directory path.
			 *
			 * @return string Full path to a private readable/writable media directory, else an exception is thrown on failure.
			 *    If the directory does NOT yet exist, it's created by this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 * @throws exception If the requested private media directory is NOT readable/writable, or CANNOT be created for any reason.
			 */
			public function get_private_media_dir($sub_dir = '')
				{
					$this->check_arg_types('string', func_get_args());

					// Creates a possible ``$sub_dir`` appendage.
					if($sub_dir && ($sub_dir = $this->©strings->trim($this->n_seps($sub_dir), '', '/')))
						{
							if(strpos($sub_dir, '..') === FALSE) // No relative paths please.
								$sub_dir = '/'.$sub_dir; // Add prefix so it can be appended easily.

							else throw $this->©exception(
								__METHOD__.'#relative_paths', compact('sub_dir'),
								$this->i18n('Expecting a sub-directory with NO relative paths.').
								sprintf($this->i18n(' Instead got: `%1$s`.'), $sub_dir)
							);
						}

					// Clean these up and piece them together.
					$media_dir        = $this->n_seps($this->___instance_config->plugin_data_dir.'/media');
					$app_data_sub_dir = ($this->©env->is_windows() && !$this->©env->is_apache()) ? '/app_data' : '';
					$media_sub_dir    = $this->n_seps($media_dir.$app_data_sub_dir.$sub_dir);

					// Need to create the ``$media_sub_dir``?
					if(!is_dir($media_sub_dir)) // Create if missing.
						{
							mkdir($media_sub_dir, 0775, TRUE);
							clearstatcache(); // Clear cache (makes other routines aware).

							if(!is_dir($media_sub_dir) || !is_readable($media_sub_dir) || !is_writable($media_sub_dir))
								throw $this->©exception(
									__METHOD__.'#read_write_issues', compact('media_sub_dir'),
									$this->i18n('Unable to create a private readable/writable `media` directory.').
									sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $media_sub_dir)
								);

							if(is_dir($media_dir) && !file_exists($media_dir.'/.htaccess'))
								file_put_contents($media_dir.'/.htaccess', 'deny from all');
						}

					// Is ``$media_sub_dir`` readable/writable?
					if(is_readable($media_sub_dir) && is_writable($media_sub_dir))
						return $media_sub_dir; // It's a good day in Eureka!

					else throw $this->©exception(
						__METHOD__.'#read_write_issues', compact('media_sub_dir'),
						$this->i18n('Unable to find a private readable/writable media directory.').
						sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $media_sub_dir)
					);
				}

			/**
			 * Empties and deletes a private media directory.
			 *
			 * @param string  $sub_dir Optional media sub-directory path.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else FALSE.
			 *    Also returns TRUE if the directory is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$sub_dir`` is a relative path (this is a NO-no, for security).
			 *
			 * @throws exception See: ``get_private_media_dir()`` for additional exceptions this may throw.
			 * @throws exception See: ``empty_and_remove()`` for additional exceptions this may throw.
			 */
			public function del_private_media_dir($sub_dir = '')
				{
					$this->check_arg_types('string', func_get_args());

					return $this->empty_and_remove($this->get_private_media_dir($sub_dir));
				}

			/**
			 * Recursively empties a directory and removes it via PHP.
			 *
			 * @param string $dir A full directory path to empty and remove.
			 *
			 * @return boolean TRUE if the directory was successfully removed, else FALSE.
			 *    Also returns TRUE if ``$dir`` is already non-existent (i.e. nothing to remove).
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If the ``$dir`` given, is NOT readable/writable, or CANNOT be opened for any reason.
			 * @throws exception If any sub-directory or file within ``$dir`` is NOT readable/writable, or CANNOT be opened for any reason.
			 *
			 * @assert ($this->object->___instance_config->plugin_data_dir) === TRUE
			 * @assert ('foo') === TRUE
			 */
			public function empty_and_remove($dir)
				{
					$this->check_arg_types('string', func_get_args());

					if(!($dir = $this->n_seps($dir)) || !is_dir($dir))
						return TRUE; // It's already gone.

					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', compact('dir'),
							$this->i18n('Unable to remove a directory; not readable, due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $dir)
						);

					else if(!is_writable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', compact('dir'),
							$this->i18n('Unable to remove a directory; not writable, due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable/writable please: `%1$s`.'), $dir)
						);

					else if(!($_open_dir = opendir($dir)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', compact('dir'),
							$this->i18n('Unable to remove a directory; cannot open, for some unknown reason.').
							sprintf($this->i18n(' Make this directory readable/writable please: `%1$s`.'), $dir)
						);

					while(($_dir_file = readdir($_open_dir)) !== FALSE) // Recursively delete all sub-directories/files.
						{
							if($_dir_file !== '.' && $_dir_file !== '..') // Ignore directory dots.
								{
									$_dir_file = $dir.'/'.$_dir_file; // We need the full path now.

									if(is_dir($_dir_file)) // Recursive call for sub-directories.
										{
											if(!$this->empty_and_remove($_dir_file))
												return FALSE; // This failed for some reason.
										}
									else if(is_writable($_dir_file)) // File MUST be writable.
										unlink($_dir_file); // We should be able to remove this file.

									else throw $this->©exception(
										__METHOD__.'#read_write_issues', compact('_dir_file'),
										$this->i18n('Unable to remove a file, due to permission issues.').
										sprintf($this->i18n(' Need this file to be writable please: `%1$s`.'), $_dir_file)
									);
								}
						}
					closedir($_open_dir); // Close directory.
					unset($open_dir, $_dir_file); // Housekeeping.

					$rmdir = rmdir($dir); // Remove empty directory now.
					clearstatcache(); // Clear cache (makes other routines aware).

					return ($rmdir) ? TRUE : FALSE; // It's a good day in Eureka?
				}

			/**
			 * Recursively copies a directory via PHP.
			 *
			 * @param string      $dir A full directory path to copy.
			 *
			 * @param string      $to A new directory path, to copy ``$dir`` to.
			 *    This new directory must NOT already exist. If it does, an exception is thrown.
			 *
			 * @param array       $exclusions Optional. Defaults to an empty array.
			 *
			 *    • A list of files/directories to ignore (using FNMATCH wildcard patterns).
			 *       The `FNM_PATHNAME` flag is NOT in use for any wildcard patterns you specify here.
			 *       A wildcard `*` WILL match directory separators. TODO: Make this a configurable option.
			 *
			 *    • Special case handler for `.gitignore` files (VERY useful).
			 *       If ``$exclusions['.gitignore']`` === `/path/to/.gitignore`; we will attempt to process GIT via command line.
			 *       We use: `git ls-files --others --directory` to compile a complete list of exclusions automatically.
			 *       This way the copied directory will reflect only the files under GIT version control.
			 *       A `.gitignore` file MUST exist in the parent of your initial ``$dir``.
			 *
			 *    Regarding wildcard pattern matching...
			 *
			 *       • Files/directories will always include a leading slash `/` when checking exclusions.
			 *          We use an absolute relative path; starting with the initial ``$dir`` that you're copying from;
			 *          (in other words, relative from the parent directory of your initial ``$dir``).
			 *
			 *          Assuming your initial value for ``$dir`` is `/path/to/dir`.
			 *          And; assuming this file exists that you want to exclude: `/path/to/dir/sub-dir/file.php`.
			 *          You will need a pattern that matches this path: `/dir/sub-dir/file.php`.
			 *
			 *       • Directories will include a trailing slash when checking exclusions.
			 *
			 *          Assuming your initial value for ``$dir`` is `/path/to/dir`.
			 *          And; assuming this directory exists that you want to exclude: `/path/to/dir/sub-dir`.
			 *          You will need a pattern that matches this path: `/dir/sub-dir/`.
			 *
			 *       • File/directory paths obtained through `git ls-files --others --directory`;
			 *          are converted into wildcard patterns automatically by this routine.
			 *          A `.gitignore` file MUST exist in the parent of your initial ``$dir``.
			 *
			 * @param boolean     $exclusions_case_insensitive Defaults to a FALSE value.
			 *    If TRUE, wildcard pattern matching is NOT case sensitive.
			 *    The `FNM_CASEFOLD` flag is enabled if this is TRUE.
			 *
			 * @param null|string $___initial_dir Internal use only. Please do NOT pass this in directly.
			 *
			 * @return boolean TRUE if the directory was successfully copied, else FALSE.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If the ``$dir`` given, is NOT a readable directory, or CANNOT be opened for any reason.
			 * @throws exception If any sub-directory or file within ``$dir`` is NOT readable, or CANNOT be opened for any reason.
			 * @throws exception If the ``$to`` directory already exists, or CANNOT be created by this routine for any reason.
			 *
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but the `.gitignore` file does not exist.
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but it's NOT in the initial ``$dir`` parent.
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but the `git` command is currently NOT possible.
			 * @throws exception If a `.gitignore` file is used for ``$exclusions``; but `git` returns a non-zero status or error msg.
			 *
			 * @see \websharks_core_v000000_dev\strings\in_wildcard_patterns()
			 * @see http://linux.die.net/man/3/fnmatch The underlying functionality applied to exclusions.
			 *
			 * @WARNING This routine can become resource intensive on large directories.
			 *    See: {@link \websharks_core_v000000_dev\env\maximize_time_memory_limits()}
			 *
			 * @assert ($this->object->___instance_config->plugin_dir, $this->object->___instance_config->plugin_dir.'-copy') === TRUE
			 */
			public function copy_to($dir, $to, $exclusions = array(), $exclusions_case_insensitive = FALSE, $___initial_dir = NULL)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', 'array', 'boolean', array('null', 'string'), func_get_args());

					if(!($dir = $this->n_seps($dir)) || !is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', compact('dir'),
							$this->i18n('Unable to copy a directory (source `dir` missing).').
							sprintf($this->i18n('Non-existent source directory: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', compact('dir'),
							$this->i18n('Unable to copy a directory; not readable due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable please: `%1$s`.'), $dir)
						);
					else if(!($_open_dir = opendir($dir)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', compact('dir'),
							$this->i18n('Unable to copy a directory; cannot open for some unknown reason.').
							sprintf($this->i18n(' Make this directory readable please: `%1$s`.'), $dir)
						);

					if(!($to = $this->n_seps($to)))
						throw $this->©exception(
							__METHOD__.'#destination_to_missing', compact('to'),
							$this->i18n('Unable to copy a directory (destination undefined).').
							sprintf($this->i18n('Undefined destination directory: `%1$s`.'), $to)
						);
					else if(is_dir($to))
						throw $this->©exception(
							__METHOD__.'#destination_to_exists', compact('to'),
							$this->i18n('Unable to copy a directory; destination already exists.').
							sprintf($this->i18n(' Please delete this directory: `%1$s`.'), $to)
						);
					else if(!is_writable(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', array('to' => $to, 'dirname' => dirname($to)),
							$this->i18n('Unable to copy a directory; destination not writable due to permission issues.').
							sprintf($this->i18n(' Need this directory to be writable please: `%1$s`.'), dirname($to))
						);
					mkdir($to, 0755, TRUE); // Create the destination directory (with recursion).
					clearstatcache(); // Clear cache before re-testing; (also makes other routines aware it exists).

					if(!is_dir($to))
						throw $this->©exception(
							__METHOD__.'#mkdir_to_failure', compact('to'),
							$this->i18n('Unable to copy a directory; destination creation failure.').
							sprintf($this->i18n(' Please create this directory: `%1$s`.'), $to)
						);

					if(!isset($___initial_dir)) $___initial_dir = $dir;

					if($dir === $___initial_dir // Only do this ONE time.
					   && isset($exclusions['.gitignore']) && $this->©string->is_not_empty($exclusions['.gitignore'])
					   && basename($exclusions['.gitignore']) === '.gitignore' // It MUST actually be a `.gitignore` file.
					) // This special array element is a `.gitignore` file; triggering this routine. Further validation occurs below.
						{
							$_gitignore_file = $exclusions['.gitignore'];
							$_gitignore_dir  = $this->©dir->n_seps(dirname($_gitignore_file));
							$_git_ls_files   = $this->©command->git.' ls-files --others --directory';
							unset($exclusions['.gitignore']); // Remove this now.

							if(!file_exists($_gitignore_file))
								throw $this->©exception(
									__METHOD__.'#nonexistent_gitignore_file', get_defined_vars(),
									sprintf($this->i18n('Nonexistent `.gitignore` file: `%1$s`.'), $_gitignore_file)
								);
							else if($_gitignore_dir !== dirname($___initial_dir))
								throw $this->©exception(
									__METHOD__.'#invalid_gitignore_file', get_defined_vars(),
									sprintf($this->i18n('Invalid `.gitignore` file: `%1$s`.'), $_gitignore_file).
									sprintf($this->i18n('Your `.gitignore` file MUST exist here: `%1$s`.'), dirname($___initial_dir).'/.gitignore')
								);
							else if(!$this->©command->git_possible()) // Possible to use GIT?
								throw $this->©exception(
									__METHOD__.'#git_command_not_possible', get_defined_vars(),
									sprintf($this->i18n('You specified the following `.gitignore` file: `%1$s`.'), $_gitignore_file).
									$this->i18n(' However, the `git` command is NOT possible in the current environment.')
								);

							$_gitignore_files = $this->©command->exec($_git_ls_files, '', TRUE, FALSE, $_gitignore_dir);

							$_gitignore_files_status = $_gitignore_files['status'];
							$_gitignore_files_errors = $_gitignore_files['errors'];
							/** @var errors $_gitignore_files_errors */

							if($_gitignore_files_status !== 0 || $_gitignore_files_errors->exist())
								throw $this->©exception(
									__METHOD__.'#gitignore_files_command_exec_issue', get_defined_vars(),
									sprintf($this->i18n('You specified the following `.gitignore` file: `%1$s`.'), $_gitignore_file).
									sprintf($this->i18n(' However, the command: `%1$s`, returned a non-zero status or error. Git said: `%2$s`'),
									        $_git_ls_files, $_gitignore_files_errors->get_message())
								);
							if($_gitignore_files['output']) // Do we have output (e.g. a list of ignored files)?
								{
									foreach(preg_split('/['."\r\n".']+/', $_gitignore_files['output'], -1, PREG_SPLIT_NO_EMPTY) as $_path)
										// No need to normalize directory separators here; GIT already does that for us.
										// Directories returned by GIT always include a trailing slash (easy to identify).
										// The list provided by GIT does NOT include leading slashes though (we add those).
										$exclusions[] = (substr($_path, -1) === '/') ? '/'.$_path.'*' : '/'.$_path;
								}
							unset($_gitignore_file, $_gitignore_dir, $_git_ls_files, $_gitignore_files, $_gitignore_files_status, $_gitignore_files_errors, $_path);
						}
					while(($_dir_file = readdir($_open_dir)) !== FALSE) // Recursively copy all sub-directories/files.
						{
							if($_dir_file !== '.' && $_dir_file !== '..') // Ignore directory dots.
								{
									$_dir_file                   = $dir.'/'.$_dir_file;
									$_dir_file_is_dir            = is_dir($_dir_file);
									$_dir_file_abs_relative_path = '/'.preg_replace('/^'.preg_quote(dirname($___initial_dir), '/').'\//', '', $_dir_file);
									$_dir_file_abs_relative_path .= ($_dir_file_is_dir) ? '/' : ''; // Trailing dir separator on actual directories.
									$_dir_file_to = $to.'/'.basename($_dir_file); // New copy location.

									if(!$exclusions || !$this->©string->in_wildcard_patterns
										($_dir_file_abs_relative_path, $exclusions, $exclusions_case_insensitive)
									) // Only if this directory/file has NOT been excluded via the ``$exclusions`` array.
										{
											if($_dir_file_is_dir) // Recursive sub-directories.
												{
													if(!$this->copy_to($_dir_file, $_dir_file_to, $exclusions, $exclusions_case_insensitive, $___initial_dir))
														return FALSE; // Return false. We failed for some reason.
												}
											else if(!is_readable($_dir_file) || !copy($_dir_file, $_dir_file_to))
												throw $this->©exception(
													__METHOD__.'#copy_to_failure', compact('to', '_dir_file', '_dir_file_to'),
													sprintf($this->i18n('Unable to copy to: `%1$s`, possible permission issues.'), $_dir_file_to).
													sprintf($this->i18n(' Need this file to be readable please: `%1$s`.'), $_dir_file)
												);
										}
								}
						}
					closedir($_open_dir); // Close directory resource handle before we unset these.
					unset($open_dir, $_dir_file, $_dir_file_is_dir, $_dir_file_abs_relative_path, $_dir_file_to);

					clearstatcache(); // Clear cache (makes other routines aware).

					return TRUE; // It's a good day in Eureka!
				}

			/**
			 * Creates a PHAR (PHP Archive) file from an entire directory.
			 *
			 * @note The entire directory (all files) will be archived recursively;
			 *    and GZIP compression will be applied intuitively (e.g. to textual files only).
			 *
			 * @param string         $dir The directory we want to create a PHAR file for.
			 * @param string         $to The new PHAR file location. This is actually a temporary location;
			 *    because PHAR files generated by this method will be renamed w/ a PHP extension by default.
			 *
			 * @param string         $stub Optional. This defaults to a value of ``framework::core``.
			 *    By default, we use the WebSharks™ Core stub. However, this can be set to a specific stub file;
			 *    for which the contents will be read; and set as the stub for the resulting PHAR file.
			 *
			 * @param boolean|string $phpify Optional. Defaults to a TRUE value (recommended).
			 *    By default, we rename the PHAR file w/ a PHP extension; making it executable on most hosting platforms.
			 *    If this is set to FALSE; the resulting PHAR file will NOT be renamed; and it will remain in the ``$to`` location as-is.
			 *    This can be passed as TRUE|FALSE. Or, if the intention is TRUE; and a string is supplied;
			 *    the file will be renamed with the string extension provided by ``$phpify``.
			 *
			 * @return string The new PHAR file path (either a `.phar` file, or a `.phar.php` file; depending on ``$phpify`` value).
			 *    See: ``$phpify`` parameter; it impacts the return value of this routine.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If ``$dir`` does NOT exist; or is NOT readable.
			 * @throws exception If ``$to`` is NOT specified (at least initially) with a `.phar` extension.
			 * @throws exception If ``$to`` parent directory does NOT exist; or is not writable.
			 * @throws \exception If any Phar class failures occur.
			 * @throws exception If ``$phpify`` is TRUE; but a rename failure occurs.
			 *
			 * @WARNING This routine can become resource intensive on large directories.
			 *    Mostly because of the compression routines applied here intuitively. It takes some time.
			 *    See: {@link \websharks_core_v000000_dev\env\maximize_time_memory_limits()}
			 *
			 * @WARNING The resulting PHAR file will be webPhar-enabled (this poses a possible security issue).
			 *    We say, "a possible security issue" because a PHAR file is NOT web-server-aware (at least — not by default).
			 *    To clarify, a PHAR file does NOT understand that certain files SHOULD be protected from public access.
			 *
			 *    WebSharks™ combats this problem with a custom webPhar stub file that will offer compatibility
			 *    with two of the most popular web servers: Apache-compatible servers & Windows® IIS.
			 *
			 *       • If you want to protect a directory on Apache-compatible servers;
			 *          place an `.htaccess` file inside the directory with the phrase `deny from all` anywhere in the file.
			 *          The WebSharks™ webPhar stub will automatically protect files inside directories with this `.htaccess` file.
			 *
			 *       • If you want to protect a directory on IIS; place files inside an `/app_data/` directory.
			 *          The WebSharks™ webPhar stub will automatically protect files inside directories with this name.
			 */
			public function phar_to($dir, $to, $stub = self::core, $phpify = TRUE)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', 'string:!empty', array('string', 'boolean'), func_get_args());

					if(!($dir = $this->n_seps($dir)) || !is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', compact('dir'),
							$this->i18n('Unable to PHAR a directory (source `dir` missing).').
							sprintf($this->i18n('Non-existent source directory: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', compact('dir'),
							$this->i18n('Unable to PHAR a directory; not readable, due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable please: `%1$s`.'), $dir)
						);

					if(!($to = $this->n_seps($to)))
						throw $this->©exception(
							__METHOD__.'#destination_to_missing', compact('to'),
							$this->i18n('Unable to PHAR a directory (destination undefined).').
							sprintf($this->i18n('Undefined destination directory: `%1$s`.'), $to)
						);
					else if(!preg_match('/\.phar$/i', $to))
						throw $this->©exception(
							__METHOD__.'#invalid_phar_file', compact('to'),
							$this->i18n('Unable to PHAR a directory; invalid destination PHAR file.').
							sprintf($this->i18n(' Please use a `.phar` file extension instead of: `%1$s`.'), $to)
						);
					else if(!is_writable(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', array('to' => $to, 'dirname' => dirname($to)),
							$this->i18n('Unable to PHAR a directory; destination not writable due to permission issues.').
							sprintf($this->i18n(' Need this directory to be writable please: `%1$s`.'), dirname($to))
						);

					if($stub === $this::core)
						$stub_file = dirname(dirname(dirname(__FILE__))).'/stub.php';
					else $stub_file = $stub; // A specific file path.

					if(!$stub_file || !file_exists($stub_file))
						throw $this->©exception(
							__METHOD__.'#missing_stub_file', compact('stub', 'stub_file'),
							$this->i18n('Unable to PHAR a directory; missing stub.').
							sprintf($this->i18n(' File does NOT exist: `%1$s`.'), $stub_file)
						);
					else if(!is_readable($stub_file))
						throw $this->©exception(
							__METHOD__.'#stub_file_issues', compact('stub', 'stub_file'),
							$this->i18n('Unable to PHAR a directory; permission issues with stub file.').
							sprintf($this->i18n(' Need this file to be writable please: `%1$s`.'), $stub_file)
						);

					// Phar classes throw exceptions on failure.
					// NOTE: This requires `php.ini` with `phar.readonly = no` (a NON-default setting).

					$_stub_file_contents = file_get_contents($stub_file);
					$_stub_is_phar       = '/static\s+\$is_phar\s*\=\s*[^;]+;\s*\/\/\s*\!#is\-phar#\!/i';
					$_stub_file_contents = preg_replace($_stub_is_phar, 'static $is_phar = TRUE;', $_stub_file_contents, 1);

					$_phar = new \Phar($to);
					$_phar->buildFromDirectory($dir);
					$_phar->setStub($_stub_file_contents);
					unset($_phar, $_stub_is_phar, $_stub_file_contents);

					$_phar = new \Phar($to);
					foreach(new \RecursiveIteratorIterator($_phar) as $_file)
						{
							/** @var $_file \PharFileInfo Inherits from SplFileInfo class. */
							$_extension = strtolower(ltrim(strrchr($_file->getFilename(), '.'), '.'));

							// TODO: This takes MUCH longer than the Phar:compressAllFiles(). Why?
							if(in_array($_extension, array('txt', 'sql', 'html', 'xml', 'php', 'css', 'js'), TRUE))
								$_file->compress(\Phar::GZ);
						}
					unset($_phar, $_file, $_extension); // Housekeeping.

					if(!$phpify) // Makes PHAR file executable on most hosting platforms.
						return $to; // Nothing more to do in this case.

					if(is_string($phpify)) // A specific string extension?
						$php = preg_replace('/\.phar$/i', '.'.trim($phpify, '.'), $to);
					else $php = preg_replace('/\.phar$/i', '.phar.php', $to);

					if(!rename($to, $php))
						throw $this->©exception(
							__METHOD__.'#rename_failure', compact('to', 'php'),
							sprintf($this->i18n('Failed to rename PHAR archive w/PHP extension: `%1$s`.'), $php)
						);
					return $php; // New PHP file location.
				}

			/**
			 * Recursively ZIPs a directory via PHP.
			 *
			 * @param string      $dir A full directory path to ZIP.
			 *
			 * @param string      $to A new ZIP file path — to ZIP ``$dir`` into.
			 *    The directory this lives in MUST already exist and be writable.
			 *    If this file already exists, an exception will be thrown.
			 *
			 * @return boolean TRUE if the directory was successfully zipped, else an exception is thrown.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 * @throws exception If the ``$dir`` is NOT a readable directory, or CANNOT be zipped for any reason.
			 * @throws exception If the ``$to`` ZIP already exists, or CANNOT be created by this routine for any reason.
			 * @throws exception If the ``$to`` ZIP does NOT end with the proper `.zip` extension.
			 * @throws exception If the ``$to`` ZIP parent directory does NOT exist or is not writable.
			 */
			public function zip_to($dir, $to)
				{
					$this->check_arg_types('string:!empty', 'string:!empty', func_get_args());

					if(!class_exists('\\PclZip'))
						include_once ABSPATH.'wp-admin/includes/class-pclzip.php';

					if(!($dir = $this->n_seps($dir)) || !is_dir($dir))
						throw $this->©exception(
							__METHOD__.'#source_dir_missing', compact('dir'),
							$this->i18n('Unable to ZIP a directory (source `dir` missing).').
							sprintf($this->i18n(' Non-existent source directory: `%1$s`.'), $dir)
						);
					else if(!is_readable($dir))
						throw $this->©exception(
							__METHOD__.'#read_write_issues', compact('dir'),
							$this->i18n('Unable to ZIP a directory; not readable; due to permission issues.').
							sprintf($this->i18n(' Need this directory to be readable please: `%1$s`.'), $dir)
						);

					if(!($to = $this->n_seps($to)))
						throw $this->©exception(
							__METHOD__.'#zip_to_missing', compact('to'),
							$this->i18n('Unable to ZIP a directory (destination undefined).').
							sprintf($this->i18n(' Undefined destination ZIP file: `%1$s`.'), $to)
						);
					else if(substr($to, -4) !== '.zip')
						throw $this->©exception(
							__METHOD__.'#invalid_zip', get_defined_vars(),
							$this->i18n('Invalid ZIP extension. The destination must end with `.zip`.').
							sprintf($this->i18n(' Instead got: `%1$s`.'), $to)
						);
					else if(file_exists($to))
						throw $this->©exception(
							__METHOD__.'#existing_zip', get_defined_vars(),
							$this->i18n('Destination ZIP exists; it MUST first be deleted please.').
							sprintf($this->i18n(' Please check this ZIP archive: `%1$s`.'), $to)
						);
					else if(!is_dir(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#zip_to_dir_missing', get_defined_vars(),
							$this->i18n('Destination ZIP directory does NOT exist yet.').
							sprintf($this->i18n(' Please check this directory: `%1$s`.'), dirname($to))
						);
					else if(!is_writable(dirname($to)))
						throw $this->©exception(
							__METHOD__.'#zip_to_dir_permissions', get_defined_vars(),
							$this->i18n('Destination ZIP directory is not writable.').
							sprintf($this->i18n(' Please check permissions on this directory: `%1$s`.'), dirname($to))
						);

					$archive = new \PclZip($to);
					if(!$archive->create($dir, PCLZIP_OPT_REMOVE_PATH, dirname($dir)))
						throw $this->©exception(
							__METHOD__.'#pclzip_archive_failure#'.$archive->errorCode(), get_defined_vars(),
							sprintf($this->i18n('PclZip archive failure: `%1$s`.'), $archive->errorInfo(TRUE))
						);
					return TRUE; // Default return value.
				}

			/**
			 * Checks the current plugin's data directory.
			 *
			 * @return boolean TRUE if the current plugin's data directory exists, and is readable/writable; else FALSE.
			 *    If the directory does NOT exist, this routine will attempt to create it before checking.
			 *
			 * @assert () === TRUE
			 */
			public function data_dir_readable_writable()
				{
					$data_dir = $this->___instance_config->plugin_data_dir;

					if(!is_dir($data_dir) && is_writable(dirname($data_dir)))
						{
							mkdir($data_dir, 0775);
							clearstatcache(); // Clear cache.
						}
					if(is_dir($data_dir) && is_readable($data_dir) && is_writable($data_dir))
						return TRUE; // Yes, we have a good data directory now.

					return FALSE; // Default return value.
				}

			/**
			 * Gets all possible template directories (ordered by precedence).
			 *
			 * @return array All possible template directories (ordered by precedence).
			 */
			public function where_templates_may_reside()
				{
					$dirs = array(
						get_stylesheet_directory().'/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						get_template_directory().'/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						$this->___instance_config->plugin_pro_dir.'/templates/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						$this->___instance_config->plugin_dir.'/templates/'.$this->___instance_config->plugin_root_ns_stub_with_dashes,
						dirname(dirname(dirname(__FILE__))).'/templates/'
					);
					return $this->apply_filters('where_templates_may_reside', $dirs);
				}

			/**
			 * Adds data/procedures associated with this class.
			 *
			 * @param boolean $confirmation Defaults to FALSE. Set this to TRUE as a confirmation.
			 *    If this is FALSE, nothing will happen; and this method returns FALSE.
			 *
			 * @return boolean TRUE if successfully installed, else FALSE.
			 *
			 * @notice Enqueues notice if we do NOT have a readable/writable data directory.
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
							if($this->data_dir_readable_writable())
								return TRUE;

							$this->©notice->enqueue(
								'<p>'.
								sprintf(
									$this->i18n('Please create this directory: <code>%1$s</code>.'),
									$this->©dirs->get_doc_root_path($this->___instance_config->plugin_data_dir)
								).
								$this->i18n(' You\'ll need to log in via FTP, and set directory permissions to <code>777</code>.').
								$this->i18n(' Please use an application like <a href="http://filezilla-project.org/" target="_blank">FileZilla™</a>.').
								$this->i18n(' See also: <a href="http://www.youtube.com/watch?v=oq0oM2w9lcQ" target="_blank">this video tutorial</a>').
								'</p>'
							);
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
							$this->empty_and_remove($this->___instance_config->plugin_data_dir);

							return TRUE;
						}
					return FALSE; // Default return value.
				}
		}
	}