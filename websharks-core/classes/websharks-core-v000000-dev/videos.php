<?php
/**
 * Videos.
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
		 * Videos.
		 *
		 * @package WebSharks\Core
		 * @since 120318
		 *
		 * @assert ($GLOBALS[__NAMESPACE__])
		 */
		class videos extends framework
		{
			/**
			 * YouTube® video (IFRAME URL).
			 *
			 * @param string $video The video ID.
			 * @param array  $args Optional arguments.
			 *
			 * @return string IFRAME URL to a YouTube® video.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 */
			public function yt_iframe_url($video, $args = array())
				{
					$this->check_arg_types('string:!empty', 'array', func_get_args());

					$url      = $this->©url->current_scheme().'://www.youtube.com/embed/'.urlencode($video);
					$defaults = array(
						'rel'         => 0,
						'enablejsapi' => 1,
						'playerapiid' => $video,
						'origin'      => $this->©url->current_host()
					);
					$args     = array_merge($defaults, $args);
					$args     = $this->©string->ify_deep($args);

					return add_query_arg(urlencode_deep($args), $url);
				}

			/**
			 * YouTube® video (IFRAME tag).
			 *
			 * @param string $video The video ID.
			 * @param array  $args Optional array of arguments.
			 * @param array  $classes Optional array of CSS classes for the IFRAME tag.
			 * @param string $attrs Optional string of additional attributes for the IFRAME tag.
			 *
			 * @return string IFRAME tag for a YouTube® video.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @see https://developers.google.com/youtube/player_parameters
			 */
			public function yt_iframe_tag($video, $args = array(), $classes = array(), $attrs = '')
				{
					$this->check_arg_types('string:!empty', 'array', 'array', 'string', func_get_args());

					$defaults = array(
						'width'  => '98%',
						'height' => '98%'
					);
					$args     = array_merge($defaults, $args);
					$args     = $this->©string->ify_deep($args);

					return '<iframe type="text/html" frameborder="0"'.

					       ' src="'.esc_attr($this->yt_iframe_url($video, $args)).'"'.

					       (($classes) ? ' class="'.esc_attr(implode(' ', $classes)).'"' : '').

					       ' style="width:'.esc_attr($args['width']).'; height:'.esc_attr($args['height']).';'.
					       ' padding:1%; background:#CCCCCC; border-radius:3px;"'.

					       (($attrs) ? ' '.$attrs : '').
					       '></iframe>';
				}

			/**
			 * YouTube® playlist (IFRAME URL).
			 *
			 * @param string $playlist The playlist ID.
			 * @param array  $args Optional array of arguments.
			 *
			 * @return string IFRAME URL to a YouTube® video playlist.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 */
			public function yt_playlist_iframe_url($playlist, $args = array())
				{
					$this->check_arg_types('string:!empty', 'array', func_get_args());

					$url      = $this->©url->current_scheme().'://www.youtube.com/embed';
					$playlist = 'PL'.preg_replace('/^PL/', '', $playlist);
					$defaults = array(
						'rel'         => 0,
						'enablejsapi' => 1,
						'playerapiid' => $playlist,
						'origin'      => $this->©url->current_host(),
						'listType'    => 'playlist',
						'list'        => $playlist
					);
					$args     = array_merge($defaults, $args);
					$args     = $this->©string->ify_deep($args);

					return add_query_arg(urlencode_deep($args), $url);
				}

			/**
			 * YouTube® playlist (IFRAME tag).
			 *
			 * @param string $playlist The playlist ID.
			 * @param array  $args Optional array of arguments.
			 * @param array  $classes Optional array of CSS classes for the IFRAME tag.
			 * @param string $attrs Optional string of additional attributes for the IFRAME tag.
			 *
			 * @return string IFRAME tag for a YouTube® video playlist.
			 *
			 * @throws exception If invalid types are passed through arguments list.
			 *
			 * @see https://developers.google.com/youtube/player_parameters
			 */
			public function yt_playlist_iframe_tag($playlist, $args = array(), $classes = array(), $attrs = '')
				{
					$this->check_arg_types('string:!empty', 'array', 'array', 'string', func_get_args());

					$defaults = array(
						'width'  => '98%',
						'height' => '98%'
					);
					$args     = array_merge($defaults, $args);
					$args     = $this->©string->ify_deep($args);

					return '<iframe type="text/html" frameborder="0"'.

					       ' src="'.esc_attr($this->yt_playlist_iframe_url($playlist, $args)).'"'.

					       (($classes) ? ' class="'.esc_attr(implode(' ', $classes)).'"' : '').

					       ' style="width:'.esc_attr($args['width']).'; height:'.esc_attr($args['height']).';'.
					       ' padding:1%; background:#CCCCCC; border-radius:3px;"'.

					       (($attrs) ? ' '.$attrs : '').
					       '></iframe>';
				}
		}
	}