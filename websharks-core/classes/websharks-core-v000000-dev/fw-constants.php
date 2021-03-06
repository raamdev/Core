<?php
/**
 * WebSharks™ Core framework constants.
 *
 * Copyright: © 2013 (coded in the USA)
 * {@link http://www.websharks-inc.com WebSharks™}
 *
 * @author JasWSInc
 * @package WebSharks\Core
 * @since 130331
 */
# -----------------------------------------------------------------------------------------------------------------------------------------
# WebSharks™ Core framework constants (interface used in the WebSharks™ Core framework base class).
# -----------------------------------------------------------------------------------------------------------------------------------------
namespace websharks_core_v000000_dev
	{
		if(!defined('WPINC'))
			exit('Do NOT access this file directly: '.basename(__FILE__));

		# -----------------------------------------------------------------------------------------------------------------------------------
		# WebSharks™ Core framework constants (interface definition).
		# -----------------------------------------------------------------------------------------------------------------------------------
		/**
		 * WebSharks™ Core framework constants.
		 *
		 * @package WebSharks\Core
		 * @since   130331
		 */
		interface fw_constants
		{
			# --------------------------------------------------------------------------------------------------------------------------------
			# Multipurpose/misc constants.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents the `core`.
			 */
			const core = '___core___';

			/**
			 * @var string Represents `all` of something.
			 */
			const all = '___all___';

			/**
			 * @var string Represents `defaults`.
			 */
			const defaults = '___defaults___';

			/**
			 * @var string Represents a reconsideration.
			 */
			const reconsider = '___reconsider___';

			/**
			 * @var string Represents own components.
			 */
			const own_components = '___own_components___';

			/**
			 * @var string Represents a direct call, as opposed to a hook/filter.
			 */
			const direct_call = '___direct_call___';

			/**
			 * @var string Represents do `echo` command.
			 */
			const do_echo = '___do_echo___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Return value types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `boolean`.
			 */
			const boolean = '___boolean___';

			/**
			 * @var string Represents `float`.
			 */
			const float = '___float___';

			/**
			 * @var string Represents `integer`.
			 */
			const integer = '___integer___';

			/**
			 * @var string Represents `object` properties.
			 */
			const object_p = '___object_p___';

			/**
			 * @var string Represents associative `array`.
			 */
			const array_a = '___array_a___';

			/**
			 * @var string Represents numeric `array`.
			 */
			const array_n = '___array_n___';

			/**
			 * @var string Represents space-separated `string`.
			 */
			const space_sep_string = '___space_sep_string___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Conditional logic types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `all` logic.
			 */
			const all_logic = '___all_logic___';

			/**
			 * @var string Represents `any` logic.
			 */
			const any_logic = '___any_logic___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Prepend/append/replace flags.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `prepend` flag.
			 */
			const prepend = '___prepend___';

			/**
			 * @var string Represents `append` flag.
			 */
			const append = '___append___';

			/**
			 * @var string Represents `replace` flag.
			 */
			const replace = '___replace___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Logging enabled/disabled flags.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents logging enabled.
			 */
			const log_enable = '___log_enable___';

			/**
			 * @var string Represents logging disabled.
			 */
			const log_disable = '___log_disable___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# RFC types (standards).
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents conformity with rfc1738.
			 */
			const rfc1738 = '___rfc1738___';

			/**
			 * @var string Represents conformity with rfc3986.
			 */
			const rfc3986 = '___rfc3986___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Regex flavors.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents PHP regex flavor.
			 */
			const regex_php = '___regex_php___';

			/**
			 * @var string Represents JavaScript regex flavor.
			 */
			const regex_js = '___regex_js___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Context types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `profile_updates` context.
			 */
			const context_registration = '___context_registration___';

			/**
			 * @var string Represents `profile_updates` context.
			 */
			const context_profile_updates = '___context_profile_updates___';

			/**
			 * @var string Represents `profile_updates` context.
			 */
			const context_profile_views = '___context_profile_views___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# String replacement types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents ``preg_replace()`` type.
			 */
			const preg_replace_type = '___preg_replace_type___';

			/**
			 * @var string Represents ``str_replace()`` type.
			 */
			const str_replace_type = '___str_replace_type___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Permission types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents a `public` type.
			 */
			const public_type = '___public_type___';

			/**
			 * @var string Represents a `protected` type.
			 */
			const protected_type = '___protected_type___';

			/**
			 * @var string Represents a `private` type.
			 */
			const private_type = '___private_type___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# URL types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `home` type.
			 */
			const home_type = '___home_type___';

			/**
			 * @var string Represents `network_home` type.
			 */
			const network_home_type = '___network_home_type___';

			/**
			 * @var string Represents `site` type.
			 */
			const site_type = '___site_type___';

			/**
			 * @var string Represents `network_site` type.
			 */
			const network_site_type = '___network_site_type___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# MIME types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `textual` type.
			 */
			const textual_type = '___textual_type___';

			/**
			 * @var string Represents `compressable` type.
			 */
			const compressable_type = '___compressable_type___';

			/**
			 * @var string Represents `cacheable` type.
			 */
			const cacheable_type = '___cacheable_type___';

			/**
			 * @var string Represents `binary` type.
			 */
			const binary_type = '___binary_type___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Filesystem types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `file` type.
			 */
			const file_type = '___file_type___';

			/**
			 * @var string Represents `dir` type.
			 */
			const dir_type = '___dir_type___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Generalized types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `any known` type.
			 */
			const any_known_type = '___any_known_type___';

			/**
			 * @var string Represents `any` type.
			 */
			const any_type = '___any_type___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# Exclusion types.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var string Represents `ignore_globs` array key.
			 */
			const ignore_globs = '___ignore_globs___';

			/**
			 * @var string Represents `ignore_extra_globs` array key.
			 */
			const ignore_extra_globs = '___ignore_extra_globs___';

			/**
			 * @var string Represents `gitignore` array key.
			 */
			const gitignore = '___gitignore___';

			# --------------------------------------------------------------------------------------------------------------------------------
			# URL parts/components bitmask for WebSharks™ Core.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var integer Indicates scheme component in a URL.
			 */
			const url_scheme = 1;

			/**
			 * @var integer Indicates user component in a URL.
			 */
			const url_user = 2;

			/**
			 * @var integer Indicates pass component in a URL.
			 */
			const url_pass = 4;

			/**
			 * @var integer Indicates host component in a URL.
			 */
			const url_host = 8;

			/**
			 * @var integer Indicates port component in a URL.
			 */
			const url_port = 16;

			/**
			 * @var integer Indicates path component in a URL.
			 */
			const url_path = 32;

			/**
			 * @var integer Indicates query component in a URL.
			 */
			const url_query = 64;

			/**
			 * @var integer Indicates fragment component in a URL.
			 */
			const url_fragment = 128;

			# --------------------------------------------------------------------------------------------------------------------------------
			# Glob bitmask w/ additional WebSharks™ Core options.
			# --------------------------------------------------------------------------------------------------------------------------------

			/**
			 * @var integer Adds a slash to each directory returned.
			 */
			const glob_mark = 1;

			/**
			 * @var integer Returns files as they appear in the directory (no sorting).
			 */
			const glob_nosort = 2;

			/**
			 * @var integer Return the search pattern if no files matching it were found.
			 */
			const glob_nocheck = 4;

			/**
			 * @var integer Backslashes do not quote metacharacters.
			 */
			const glob_noescape = 8;

			/**
			 * @var integer Expands `{a,b,c}` to match `'a'`, `'b'`, or `'c'`.
			 */
			const glob_brace = 16;

			/**
			 * @var integer Return only directory entries which match the pattern.
			 */
			const glob_onlydir = 32;

			/**
			 * @var integer Stop on read errors.
			 */
			const glob_err = 64;

			/**
			 * @var integer Use `[aA][bB]` to test caSe variations.
			 */
			const glob_casefold = 128;

			/**
			 * @var integer Finds hidden dot `.` files w/ wildcards.
			 */
			const glob_period = 256;
		}
	}
