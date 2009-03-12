<?php
class deviantThumbsCarousel {
	public function carousel($query, $args = '') {
		extract(wp_parse_args($args, array(
			'id' => 'da-carousel',
			'count' => 6,
			'show' => 3,
			'rand'  => true,
			'speed' => 'fast',
			'cache' => 6
		)), EXTR_SKIP);

		$before = "\t\t<li>";
		$after = "</li>";

		$thumbs = deviantThumbs::get($query, compact('count', 'rand', 'cache', 'before', 'after'));

		$output = self::maybe_add_scripts();

		$output .= "
<div id='$id'>
	<ul>
$thumbs
	</ul>
</div>
<script language='javascript' type='text/javascript'>
jQuery(window).load(function() {
	jQuery('#$id').simpleCarousel($show, '$speed');
});
</script>
";

		return $output;
	}

	private function maybe_add_scripts() {
		global $wp_scripts;

		if ( $scripts_done )
			return;
		static $scripts_done = true;

		$carousel_url = self::get_plugin_url() . '/inc/carousel';

		$scriptf = "<script language='javascript' type='text/javascript' src='%s'></script>";

		if ( ! @in_array('jquery', $wp_scripts->done) )
			$code[] = sprintf($scriptf, get_option('siteurl') . "/wp-includes/js/jquery/jquery.js");

		$code[] = sprintf($scriptf, $carousel_url . '/carousel.js');

		$code[] = "<script language='javascript' type='text/javascript'>include_css('$carousel_url/carousel.css');</script>";

		return implode("\n", $code);
	}

	private function get_plugin_url() {
		if ( function_exists('plugins_url') )
			return plugins_url(plugin_basename(dirname(__FILE__)));
		else
			// Pre-2.6 compatibility
			return get_option('siteurl') . '/wp-content/plugins/' . plugin_basename(dirname(__FILE__));
	}
}

// Template tag
function deviant_thumbs_carousel($query, $args = '') {
	echo deviantThumbsCarousel::carousel($query, $args);
}

