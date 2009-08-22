<?php

abstract class deviantThumbsCarousel 
{
	static $carousels = array();

	function carousel($query, $args = '')
	{
		$args = wp_parse_args($args, array(
			'count' => 6,
			'show' => 3,
			'rand'  => true,
			'speed' => 'fast',
			'cache' => 6,

			'id' => sanitize_title_with_dashes($query),
			'before' => "\t\t<li>",
			'after' => "</li>"
		));

		self::$carousels[] = $args;

		$thumbs = deviantThumbs::get($query, $args);

		$output = "<div id='" . $args['id'] . "'><ul>\n{$thumbs}</ul></div>\n";

		add_action('wp_footer', array(__CLASS__, 'add_scripts'));

		return $output;
	}

	function add_scripts()
	{
		global $wp_scripts;

		$carousel_url = plugin_dir_url(__FILE__) . 'inc/';

		$scriptf = "\n<script language='javascript' type='text/javascript' src='%s'></script>";

		if ( ! @in_array('jquery', $wp_scripts->done) )
			$code[] = sprintf($scriptf, get_option('siteurl') . "/wp-includes/js/jquery/jquery.js");

		$code[] = sprintf($scriptf, $carousel_url . 'carousel.js');

		echo "\n<!--Deviant Thumbs Carousel [begin]-->";
		echo implode('', $code);

		$code = '';
		foreach ( self::$carousels as $i ) {
			extract($i);
			$code[] = "\tsimpleCarousel('#{$id}', {$show}, '{$speed}');";
		}

		echo "\n<script language='javascript' type='text/javascript'>\n";
		echo "include_css('" . $carousel_url . "carousel.css');\n";
		echo "jQuery(window).load(function() {\n" . implode("\n", $code) . "\n});\n";
		echo "</script>";
		echo "\n<!--Deviant Thumbs Carousel [end]-->";
	}
}

// WP < 2.8
if ( !function_exists('plugin_dir_url') ) :
function plugin_dir_url($file) 
{
	// WP < 2.6
	if ( !function_exists('plugins_url') )
		return trailingslashit(get_option('siteurl') . '/wp-content/plugins/' . plugin_basename($file));

	return trailingslashit(plugins_url(plugin_basename(dirname($file))));
}
endif;

// Template tag
function deviant_thumbs_carousel($query, $args = '')
{
	echo deviantThumbsCarousel::carousel($query, $args);
}

